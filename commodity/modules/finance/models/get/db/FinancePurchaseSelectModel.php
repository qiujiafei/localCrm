<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/24
 * Time: 10:38
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\finance\models\get\db;

use commodity\modules\purchase\models\PurchaseCommodityLogicModel;
use commodity\modules\purchase\models\PurchaseLogicModel;
use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityBatchAR;
use common\ActiveRecord\FinancePurchaseAR;
use common\ActiveRecord\PurchaseAR;
use common\ActiveRecord\PurchaseCommodityAR;
use yii\data\Pagination;
use yii\db\ActiveQuery;

class FinancePurchaseSelectModel extends FinancePurchaseAR
{
    /**
     * 获取符合条件的所有集合
     * @param $where   其中的like为采购财务的like模糊查询
     * @return ActiveQuery
     */
    public function getStatisticsOfMonthQuery($where)
    {
        $query = self::find()->alias('fp')
            ->where(['cb.store_id' => $where['store_id']])
            ->andWhere($where['like'])
            ->leftJoin('{{%commodity_batch}} cb','fp.commodity_batch_id = cb.id AND fp.commodity_id = cb.commodity_id')
            ->with('purchase')
            ->with('commodityBatch');
            //->groupBy('c.id');

        return $query;
    }

    /**
     * 通过批次表关联采购主表
     * @return \yii\db\ActiveQuery
     */
    public function getPurchase()
    {
        return self::hasOne(PurchaseAR::className(),['number' => 'purchase_number'])
            ->viaTable(CommodityBatchAR::tableName(),['id' => 'commodity_batch_id']);
    }

    /**
     * 批次
     * @return ActiveQuery
     */
    public function getCommodityBatch()
    {
        return self::hasMany(CommodityBatchAR::className(),['id' => 'commodity_batch_id']);
    }

    /**
     * 创建返回值
     * @param $monthLadder
     * @param $where
     * @return array
     */
    public function createStatisticsDataOfMonth($monthLadder,$where)
    {
        //重建该数组，并赋值为0;
        $monthLadders = array_combine($monthLadder,array_fill(0,count($monthLadder),0));

        $where['like'] = $this->createMonthsLadderLike($monthLadder);
        //获取查询对象
        $query = $this->getStatisticsOfMonthQuery($where);
        $data = [];
        $Ym = '';
        //批次临时数组，用于记录采购批次信息，以此判断哪些批次未减去优惠价，之所以这样处理，是因为财务表中，无法计算该商品被优惠了多少，该值未记录
        $purchaseNumber = [];
        //循环数据进行统计计算
        
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {

                $data = $model->toArray();
                $purchase = $model->purchase;

                $Ym = substr($data['created_time'],0,7);
                if (isset($monthLadders[$Ym])) {

                    if ( ! isset($purchaseNumber[$purchase->id])) {
                        $monthLadders[$Ym] += $purchase->settlement_price;
                    }
                }
                if ( ! isset($purchaseNumber[$purchase->id])) {
                    $purchaseNumber[$purchase->id] = true;
                }
            }
        }

        $return = $this->formatData($monthLadders);
        $return['total'] = PurchaseLogicModel::getSettlementPrice($where['store_id']);
        return $return;
    }

    /**
     * 格式化返回数据
     * @param array $data
     * @return mixed
     */
    private function formatData($data=[])
    {
        //月度统计
        $return['months'] = array_map(function($price){
            return number_format($price,2);
        },$data);
        //总额
        //$return['total'] = number_format(array_sum($data),2);
        return $return;
    }


    /**
     * 创建月份阶梯查询条件
     * @param $monthLadder
     * @return array
     */
    private function createMonthsLadderLike($monthLadder)
    {
        $return = [];
        foreach ($monthLadder as $month) {
            $return[] = ['like','fp.created_time',$month.'%',false];
        }

        array_unshift($return,'or');
        return $return;
    }

    /**
     * 获取采购金额列表
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public function findListsByCommodity($where,$pageSize=20)
    {
        $query = self::find()
            ->alias('fp')
            ->where(['cb.store_id' => $where['store_id']])
            ->andFilterWhere($where['between'])
            ->leftJoin('{{%commodity_batch}} cb','cb.id = fp.commodity_batch_id and cb.commodity_id = fp.commodity_id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $query = $query
            ->with('commodity')
            ->with([
                'purchaseBatch' => function($query) use ($where) {
                    $query->andWhere(['store_id'=>$where['store_id']]);
                }
            ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_time DESC');

        $list = [];
        $temp = [];
        $commodity = [];
        $total_price = 0;
        foreach($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                $batch = $model->purchaseBatch->toArray();
                //$temp = $model->toArray();
                $commodity = $model->commodity->toArray();
                //商品名称
                $temp['commodity_name'] = $commodity['commodity_name'];
                //商品销售价格
                $temp['price'] = $commodity['price'];
                //递增采购金额
                $total_price += $batch['cost_price'];
                //采购金额
                $temp['purchase_price'] = $batch['cost_price'];
                $temp['purchase_time'] = $model->purchaseNumberOfTimes;
                $list[] = $temp;
            }
        }

        foreach($list as $key => $data) {
            //金额比例
            $list[$key]['price_ratio'] = round(($data['purchase_price'] / $total_price) * 100,2) . '%';
        }

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount,
            'total_price' => $total_price
        ];
    }

    /**
     * 采购统计列表
     * 商品名称，商品售价，采购数量，数量占比
     * @param $storeId
     * @param $startTime
     * @param $endTime
     * @param int $pageSize
     * @return array
     */
    public function findListByPurchaseCommodityOfStore($storeId,$startTime,$endTime,$pageSize=20)
    {
        $where = [
            'and',
            ['cb.store_id' => $storeId],
            ['between','fp.created_time',$startTime,$endTime]
        ];

        $query = self::find()->alias('fp')->where($where)
            ->leftJoin('{{%commodity_batch}} cb','cb.id = fp.commodity_batch_id and cb.commodity_id = fp.commodity_id')
            ->leftJoin('{{%purchase}} p ','cb.purchase_number = p.number and p.status = 1 and cb.purchase_number = p.number');

        $count = $query->select('fp.commodity_id')->groupBy('fp.commodity_id')->count();
        $query->select = null;
        $query->groupBy = null;

        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $query = $query
            ->select('fp.*')
            ->with([
                'purchaseBatch' => function($query) use ($storeId){
                    return $query->where(['store_id' => $storeId]);
                }
            ])
            ->with('commodity')
            ->with('purchase')
            ->with('purchaseBatch')
            ->limit($pagination->limit)
            ->offset($pagination->offset);

        $list = $temp =[];
        $commodityNumber = [];
        $total_number = 0;
        $filterWhere = [];

        foreach ($query->batch(100) as $rows){
            foreach ($rows as $model){
                if (null === $model) {
                    continue;
                }

                $commodityId = $model->commodity_id;
                //商品名称
                $temp['commodity_name'] = $model->commodity->commodity_name;
                //商品售价
                $temp['price'] = $model->commodity->price;
                //采购数量，即该商品所有采购量
                if ( ! isset($temp['purchase_count'])) {
                    $temp['purchase_count'] = 0;
                }

                if ( ! $model->purchase) {
                    continue;
                }

                $filterWhere = [
                    'commodity_id' => $commodityId,
                    'store_id' => $storeId,
                    'purchase_id' => $model->purchase->id,
                    'depot_id' => $model->purchaseBatch->depot_id
                ];

                //商品采购数量
                if ( ! isset($commodityNumber[$commodityId])) {
                    $commodityNumber[$commodityId] = 0;

                }
                $commodityNumber[$commodityId] += PurchaseCommodityLogicModel::getQuantity($filterWhere);



                $temp['purchase_count'] =  $commodityNumber[$commodityId];
                //合并值
                $list[$commodityId] = $temp;
                $temp['purchase_count'] = 0;
            }
        }

        //总采购次数
        $total_number = array_sum($commodityNumber);

        //比例计算
        foreach($list as $key => $data) {
            //采购比例
            $list[$key]['purchasing_ratio'] = $total_number == 0 ? '0.00%' : round(($data['purchase_count'] / $total_number) * 100,2) . '%';
        }

        sort($list);

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => count($list),
            'purchase_total_number' => $total_number
        ];
    }

    /**
     * 采购商品信息
     * @return ActiveQuery
     */
    public function getCommodity()
    {
        return $this->hasOne(CommodityAR::className(),['id'=>'commodity_id']);
    }

    /**
     * 采购批次
     * @return ActiveQuery
     */
    public function getPurchaseBatch()
    {
        return $this->hasOne(CommodityBatchAR::className(),['id'=>'commodity_batch_id','commodity_id'=>'commodity_id']);
    }

    /**
     * 获取采购商品
     */
    public function getPurchaseCommodity()
    {
        return self::hasMany(PurchaseCommodityAR::className(),['purchase_number' => 'number'])
            ->viaTable(PurchaseAR::className(),['number' => 'purchase_number'])
            ->viaTable(CommodityBatchAR::tableName(),['id' => 'commodity_batch_id']);
    }

    /**
     * 商品采购次数
     * @return int|string
     */
    public function getPurchaseNumberOfTimes()
    {
        return $this->hasOne(CommodityBatchAR::className(),['commodity_id'=>'commodity_id'])
            ->count();
    }
}