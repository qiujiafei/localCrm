<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/2
 * Time: 17:16
 */
namespace commodity\modules\inventory\logics;
use common\ActiveRecord\CommodityAR;
use \common\ActiveRecord\CommodityBatchAR;
use common\ActiveRecord\DepotAR;
use common\ActiveRecord\StoreAR;
use common\ActiveRecord\UnitAR;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use yii\data\Pagination;

class CommodityBatchLogicModel extends CommodityBatchAR
{

    /**
     * 是否是当前门店采购的批次
     * @param $storeId
     * @param $commodityId
     * @return bool
     */
    public static function isStoreBatchByCommodityId($storeId,$commodityId)
    {
        $where = [
            'store_id' => $storeId,
            'commodity_id' => $commodityId
        ];
        return self::find()->where($where)->exists();
    }

    /**
     * 检查该批次是否存在
     * @param $batchId
     * @return bool
     */
    public static function isExistsIdOfStore($batchId)
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $where = [
            'id' => $batchId,
            'store_id' => $storeId
        ];
        return self::find()->where($where)->exists();
    }

    /**
     * 获取商品数据
     * @return \yii\db\ActiveQuery
     */
    public function getCommodity()
    {
        return $this->hasOne(CommodityAR::className(),['id'=>'commodity_id']);
    }

    /**
     * 关联查询仓库
     */
    public function getDepot()
    {
        return $this->hasOne(DepotAR::className(),['id'=> 'depot_id']);
    }

    /**
     * 关联查询门店
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(StoreAR::className(),['id' => 'store_id']);
    }

    /**
     * 获取单位信息
     *
     */
    public function getUnit()
    {
        return $this->hasOne(UnitAR::className(),['id' => 'unit_id'])->viaTable('{{%commodity}}',['id'=>'commodity_id']);
    }

    public static function findList($where,$pageSize=20)
    {
        $query = self::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $list = $query
            ->where($where)
            ->with('commodity')
            ->with('depot')
            ->with('store')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_time DESC')
            ->all();

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];
    }

    /**
     * 盘点时，更新库存
     * @param array $inventoryCommodityData
     * @return bool|int
     */
    public static function updateBatchByInventory($inventoryCommodityData=[])
    {
        $bool = true;
        $where = $data = [];
        foreach ($inventoryCommodityData as $key=>$value) {
            if ($value['quantity'] != $value['inventory_quantity']) {
                $where = [
                    //这里id当作库存批次号了，后期会改变
                    'id' => $value['commodity_batch_id'],
                    'commodity_id' => $value['commodity_id']
                ];
                $data = [
                    'stock' => $value['quantity']
                ];

                //更新
                $bool = self::updateAll($data,$where);
                if ( ! $bool) {
                    $bool = false;
                    break;
                }
            }
        }
        return $bool;
    }

    /**
     * 通过商品ID和入库批次号查询供应商ID
     * @param $commodityId
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getSupplierIdByCommodityIdAndId($commodityId,$id)
    {
        $where = [
            'id' => $id,
            'commodity_id' => $commodityId
        ];
        return self::find()->where($where)->select('supplier_id')->one();
    }

    /**
     * 采购时，添加新品入库
     * @param $data
     * @return bool
     */
    public static function insertBatchData($data)
    {
        $bool = true;
        $insertData = [];
        $purchase = $data['purchases'];
        $date = date('Y-m-d H:i:s');
        foreach ($data['commodities'] as $key=>$value)
        {
            $insertData['commodity_id'] = $value['commodity_id'];
            $insertData['store_id'] = $purchase['store_id'];
            $insertData['stock'] = $value['quantity'];
            $insertData['depot_id'] = $value['depot_id'];
            $insertData['purchase_number'] = $purchase['number'];
            $insertData['supplier_id'] = $purchase['supplier_id'];
            $insertData['purchase_time'] = $date;
            //成本价
            $insertData['cost_price'] = $value['current_price'];
            $insertData['created_by'] = $purchase['purchase_by'];
            $insertData['last_modified_by'] = $purchase['purchase_by'];
            $insertData['created_time'] = $date;
            $insertData['last_modified_time'] = $date;
            $model = new self($insertData);
            $bool = $model->save();
            if ( ! $bool) {
                break;
            }
        }
        return $bool;
    }

    /**
     * 批次表中，查询符合盘的数据
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public static function findListOfAllowCommodity($where,$pageSize=20)
    {
        $query = self::find()->alias('cb')->where($where)->leftJoin('{{%commodity}} c','c.id = cb.commodity_id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $list = $query
            ->with('depot')
            ->with('commodity')
            ->with('unit')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_time DESC')
            ->all();
        $lists = $temp = [];

        foreach($list as $model){
            if (null === $model) {
                continue;
            }
            $depot = $model->depot;
            $commodity = $model->commodity;
            $unit = $model->unit;

            //批次ID
            $temp['commodity_batch_id'] = $model->id;
            //商品ID
            $temp['commodity_id'] = $commodity->id;
            //商品名称
            $temp['commodity_name'] = $commodity->commodity_name;
            //商品类别名称
            $temp['classification_name'] = $commodity->classification_name;
            //商品规格
            $temp['specification'] = $commodity->specification;
            //商品编码
            $temp['commodity_code'] = $commodity->commodity_code;
            //商品条码
            $temp['barcode'] = $commodity->barcode;
            //商品售价
            $temp['price'] = $model->cost_price;
            //采购时间
            $temp['created_time'] = $model->created_time;
            //库存数量
            $temp['stock'] = $model->stock;
            //仓库ID
            $temp['depot_id'] = $depot->id;
            //仓库名称
            $temp['depot_name'] = $depot->depot_name;
            //单位名称
            $temp['unit_name'] = $unit->unit_name;
            //单位ID
            $temp['unit_id'] = $unit->id;
            //供应商ID
            $temp['supplier_id'] = $model->supplier_id;
            $lists[] = $temp;
        }

        return [
            'lists' => $lists,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];
    }
}