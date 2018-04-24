<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models\get\db;

use commodity\modules\purchase\models\PurchaseLogicModel;
use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\DepotAR;
use common\ActiveRecord\EmployeeUserAR;
use common\ActiveRecord\PurchaseAR;
use common\ActiveRecord\PurchaseCommodityAR;
use common\ActiveRecord\SupplierAR;
use yii\data\Pagination;

class SelectModel extends PurchaseAR
{
    public $commodityName;
    private $pagination;
    /**
     * 关联查询商品信息
     *
     */
    public function getCommodity()
    {
        return $this->hasMany(CommodityAR::className(),['id' => 'commodity_id'])
            ->viaTable('{{%purchase_commodity}}',['purchase_id'=>'id']);
    }

    /**
     * 关联查询仓库信息
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return $this->hasOne(DepotAR::className(),['id' => 'depot_id']);
    }

    /**
     * 关联查询单据商品信息
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseCommodity()
    {
        return $this->hasMany(PurchaseCommodityAR::className(),['purchase_id'=>'id']);
    }

    /**
     * 关联查询供应商信息
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(SupplierAR::className(),['id' => 'supplier_id']);
    }

    /**
     * 通过采购单创建者关联查询采购者信息
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(EmployeeUserAR::className(),['id' => 'created_by']);
    }

    /**
     * 查询并返回一条数据
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function createOneDataById($id)
    {
        $model = self::findOne($id);
        if (null === $model) {
            throw new \Exception('未定义数据',12006);
        }
        $data = $model->toArray();
        //商品信息
        $data['commodity'] = $model->commodity;
        $data['purchaseCommodity'] = $model->purchaseCommodity;
        return $data;
    }

    /**
     * 获取采购单详情
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function getOneDataById($id)
    {
        $model = self::findOne($id);
        if (null === $model) {
            throw new \Exception('未定义数据',12006);
        }
        return $model->toArray();
    }

    /**
     * 查询列表
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public function findList($where,$pageSize=20)
    {
        $query = self::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $this->pagination = $pagination;

        return $query
            ->where($where)
            ->with('commodity')
            ->with('purchaseCommodity')
            ->with('supplier')
            ->with('user')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('created_time DESC')
            ->all();
    }

    /**
     * 日采购统计
     * @param $storeId
     * @return mixed|string
     */
    public static function getStatisticsByToday($storeId)
    {
        //获取今天凌晨
        $startTime = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d'),date('Y')));
        //今天结束
        $endTime = date('Y-m-d H:i:s',mktime(23,59,59,date('m'),date('d'),date('Y')));
        $where = ['store_id'=>$storeId];

        $price = self::find()
            ->where($where)
            ->andWhere(
                ['!=','status',PurchaseLogicModel::PURCHASE_STATUS['zero']]
            )
            ->andFilterWhere(['between','created_time',$startTime,$endTime])
            ->sum('settlement_price');

        return $price ?? '0.00';
    }

    /**
     * 月采购统计
     *  SQL demo: SELECT SUM(origin_price) FROM `crm_purchase` WHERE (`store_id`=1) AND (`status` != 0) AND (`number` LIKE '20180201%' OR `number` LIKE '20180202%')
     * @param $storeId
     * @return string
     */
    public static function getStatisticsByMonth($storeId)
    {
        //获取本月，从1日到今日的时间序列
        $monthStart = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),1,date('Y')));
        $monthEnd = date('Y-m-d H:i:s',mktime(23,59,59,date('m'),date('t'),date('Y')));

        //查询条件
        $where = ['store_id'=>$storeId];
        $price = self::find()
            ->where($where)
            ->andWhere(
                ['!=','status',PurchaseLogicModel::PURCHASE_STATUS['zero']]
            )
            ->andFilterWhere(['between','created_time',$monthStart,$monthEnd])
            ->sum('settlement_price');

        return $price ?? '0.00';
    }
    /**
     * 所有采购统计
     * @param $storeId
     * @return mixed|string
     */
    public static function getStatisticsByAll($storeId)
    {
        $where = [
            'store_id' => $storeId,
        ];
        $price = self::find()
            ->where($where)
            ->andWhere(
                ['!=','status',PurchaseLogicModel::PURCHASE_STATUS['zero']]
            )
            ->sum('settlement_price');

        return $price ?? '0.00';
    }

    /**
     * 是否是当前门店的采购单
     * @param $id  采购单ID
     * @param $storeId  门店ID
     * @return bool
     */
    public static function isExistsPurchaseByStoreId($id,$storeId)
    {
        $where = [
            'id' => $id,
            'store_id' => $storeId
        ];

        return self::find()->where($where)->exists();
    }

    /**
     * 获取分页对象
     * @return mixed
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
