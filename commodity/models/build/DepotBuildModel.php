<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/11
 * Time: 17:15
 */

namespace commodity\models\build;

use commodity\models\interfaces\ValidateDepotIdInterface;
use commodity\models\interfaces\ValidateDepotNameInterface;
use common\ActiveRecord\DepotAR;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class DepotBuildModel extends DepotAR implements ValidateDepotNameInterface,ValidateDepotIdInterface
{
    /**
     * @param $depotName  仓库名称
     * @author hejinsong@9daye.com.cn
     * @return bool
     */
    public static function isValidDepot($depotName)
    {
        $depotName = filter_var($depotName,FILTER_CALLBACK,['options' => 'trim']);

        if ( ! isset($depotName[0]) ){
            return false;
        }
        if ( ! $storeId = AccessTokenAuthentication::getUser(true) ){
            return false;
        }
        $where = ['store_id' => $storeId,'depot_name' => $depotName];

        return self::find()->where($where)->count() > 0 ? true : false;
    }

    /**
     * @param $depotId  仓库主键ID
     * @author hejinsong@9daye.com.cn
     * @return bool
     */
    public static function isValidDepotId($depotId)
    {
        if ( ! $depotId || ! is_numeric($depotId)) {
            return false;
        }

        if ( ! $storeId = AccessTokenAuthentication::getUser(true) ){
            return false;
        }

        $where = ['store_id' => $storeId,'id' => $depotId];

        return self::find()->where($where)->count() > 0 ? true : false;
    }
}
