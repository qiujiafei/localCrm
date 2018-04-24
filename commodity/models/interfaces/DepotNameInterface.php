<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/15
 * Time: 15:47
 */

namespace commodity\models\interfaces;

interface DepotNameInterface extends DepotInterface
{
    /**
     * 是否可删除当前仓库
     * @param $depotName
     * @param $storeId
     * @return boolean
     */
    public static function isDeletable($depotName,$storeId);
}