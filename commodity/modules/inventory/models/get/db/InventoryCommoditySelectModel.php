<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/16
 * Time: 14:50
 * 盘点商品表查询
 */

namespace commodity\modules\inventory\models\get\db;

use common\ActiveRecord\InventoryAR;
use common\ActiveRecord\InventoryCommodityAR;

class InventoryCommoditySelectModel extends InventoryCommodityAR
{
    /**
     * 查询当前门店该商品是否有未被作废的盘点单据
     * @param $commodityId
     * @param $storeId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function isInvalidByCommodityIdOfInventory($commodityId,$storeId)
    {
        return self::find()
            ->where(['commodity_id'=>$commodityId])
            ->with(
                ['inventories' => function($query) use ($storeId){
                    return $query->where(['store_id' => $storeId,'status' => 1]);
                }]
            )
            ->all();
    }

    /**
     * 关联查询盘点单据，一对多
     * @return \yii\db\ActiveQuery
     */
    public function getInventories()
    {
        return self::hasMany(InventoryAR::className(),['id' => 'inventory_id']);
    }
}