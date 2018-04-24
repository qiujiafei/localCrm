<?php
/**
 * 商品ID验证
 * @author hejinsong@9daye.com.cn
 * @Date: 2018/1/24
 * @Time: 13:42
 */

namespace commodity\models\interfaces;

interface ValidateCommodityIdInterface extends CommodityInterface
{
    /**
     * @param $commodityId 商品ID
     * @return mixed
     */
    public static function isValidIdOfStore($commodityId);
}