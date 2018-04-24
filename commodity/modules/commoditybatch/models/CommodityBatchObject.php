<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/12
 * Time: 18:02
 */

namespace commodity\modules\commoditybatch\models;
use common\ActiveRecord\CommodityBatchAR;

class CommodityBatchObject
{
    /**
     * 盘点该仓库是否有库存
     * @param $storeId
     * @param $depotId
     * @return bool
     */
    public function isInStockOfDepot($storeId,$depotId)
    {
        if ( ! $storeId) {
            throw new UserException('门店ID必须',8012);
        }
        if ( ! $depotId) {
            throw new UserException('仓库ID必须',8013);
        }

        $where = [
            'store_id' => $storeId,
            'depot_id' => $depotId
        ];
        return CommodityBatchAR::find()->where($where)->sum('stock') ? true : false;
    }
}