<?php
/**
 * Date: 2018/1/26
 * Time: 13:42
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description 采购单单据相关逻辑
 */

namespace commodity\modules\purchase\logic;
use common\number\NumberFactory;
class PurchaseNumberLogic
{
    public static function createNumberFactory(String $origin='01')
    {
        $factory = new NumberFactory($origin);
        echo $factory->createNumber();
    }
}