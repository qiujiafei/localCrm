<?php

/**
 * Date: 2018/1/26
 * Time: 16:30
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description  采购单单号生成
 */
namespace common\number;

class PurchaseNumber extends NumberDecorator
{
    const TYPE = '01';

    public function __construct(NumberDecorator $numberObj)
    {
        $this->numberObj = $numberObj;
        static::$type = self::TYPE;
    }

}