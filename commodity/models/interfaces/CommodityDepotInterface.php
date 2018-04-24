<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/15
 * Time: 9:33
 */

namespace commodity\models\interfaces;

interface CommodityDepotInterface extends DepotInterface
{
    /**
     * 重命名仓库名称
     * @param $newName
     * @param $oldName
     * @param $storeId
     * @return boolean|exception  为真，表示重命名成功，其他表示失败。异常则抛出，需要返回错误码
     *
     */
    public static function renameDepotName($newName,$oldName,$storeId);
}
