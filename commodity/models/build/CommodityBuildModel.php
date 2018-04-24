<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/24
 * Time: 13:45
 */

namespace commodity\models\build;

use commodity\models\interfaces\ValidateCommodityIdInterface;
use common\ActiveRecord\CommodityAR;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class CommodityBuildModel extends CommodityAR implements ValidateCommodityIdInterface
{
    /**
     * @param $commodityId
     * @return bool
     */
    public static function isValidIdOfStore($commodityId) : bool
    {
        if ( ! $commodityId || ! is_numeric($commodityId)) {
            return false;
        }
        if ( ! $storeId = AccessTokenAuthentication::getUser(true) ) {
            return false;
        }
        $where = [
            'id' => $commodityId, 'store_id' => $storeId
        ];

        return self::find()->where($where)->exists();
    }
}