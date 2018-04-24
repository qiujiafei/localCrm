<?php
/**
 * Date: 2018/1/26
 * Time: 17:25
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description  盘点单号生成
 */
namespace common\number;

class InventoryNumber extends NumberDecorator
{
    const TYPE = '05';

    public function __construct(NumberDecorator $numberObj)
    {
        $this->numberObj = $numberObj;
        static::$type = self::TYPE;
    }

}