<?php
/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models\get\db;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\DepotAR;
use common\ActiveRecord\EmployeeUserAR;
use common\ActiveRecord\PurchaseAR;
use common\ActiveRecord\PurchaseCommodityAR;
use common\ActiveRecord\SupplierAR;
use yii\data\Pagination;

class PurchaseCommoditySelectModel extends PurchaseCommodityAR
{
    //默认上次采购价格，为0.00
    const DEFAULT_LAST_PURCHASE_PRICE = '0.00';

    public static function getLastPriceByCommodityId(int $commodityId,$storeId)
    {
        $where = [
            'commodity_id' => $commodityId,
            'store_id' => $storeId
        ];
        $model = self::find()->where($where)->orderBy('created_time DESC')->one();
        if (null === $model) {
            return self::DEFAULT_LAST_PURCHASE_PRICE;
        }
        return $model->current_price ?? self::DEFAULT_LAST_PURCHASE_PRICE;
    }

    /**
     * 返回已采购商品列表
     * @param $purchaseId  采购单ID
     * @param $pageSize
     * @return array
     */
    public static function findListByPurchaseId($purchaseId,$pageSize=20)
    {
        $where = [
            'purchase_id' => $purchaseId
        ];
        $query = self::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $list = $query
            ->where($where)
            ->with('commodity')
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

    public function getCommodity()
    {
        return $this->hasOne(CommodityAR::className(),['id'=>'commodity_id'])->select('id,commodity_name');
    }

    public function getPurchase()
    {
        return $this->hasOne(PurchaseAR::className(),['id'=>'purchase_id']);
    }

    public function getUser()
    {
        return $this->hasOne(EmployeeUserAR::className(),['id'=>'created_by'])->viaTable('{{%purchase}}',['id'=>'purchase_id']);
    }

    public static function findListForDetail($where,$pageSize=20)
    {
        $query = self::find()
            ->alias('pc')
            ->where($where)
            ->leftJoin('{{%purchase}} p','p.id = pc.purchase_id');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $list = $query
            ->with('commodity')
            ->with('purchase')
            ->with('user')
            ->with('supplier')
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
     * 获取该商品是否有作废单据与之关联
     * @param $commodityId
     * @param $storeId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function isInvalidByCommodityIdOfPurchase($commodityId,$storeId)
    {
        return self::find()
            ->where(['commodity_id' => $commodityId,'store_id' => $storeId])
            ->with([
                'purchases' => function($query)  use ($storeId) {
                    return $query->where(['store_id' => $storeId,'status' => 3]);
                }
            ])
            ->all();
    }

    /**
     * 关联获取采购单
     * @return \yii\db\ActiveQuery
     */
    public function getPurchases()
    {
        return self::hasMany(PurchaseAR::className(),['id' => 'purchase_id']);
    }

    /**
     * 获取供应商
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return self::hasOne(SupplierAR::className(),['id' => 'supplier_id'])->viaTable('{{%purchase}}',['id' => 'purchase_id']);
    }

    /**
     * 获取仓库
     * @return \yii\db\ActiveQuery
     */
    public function getDepot()
    {
        return self::hasOne(DepotAR::className(),['id' => 'depot_id']);
    }

    /**
     * 获取总量
     * @param $where
     * @return int|string
     */
    public function getCount($where)
    {
        return self::find()->where($where)->count();
    }

    /**
     * 获取数量
     * @param $where
     * @return \yii\db\ActiveQuery
     */
    public function getQuantityQuery($where)
    {
        return self::find()->where($where);
    }
}