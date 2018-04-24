<?php
/**
 * Date: 2018/1/26
 * Time: 09:22
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description  退货单号生成
 */
namespace common\number;

class RefundOfGoodsNumber extends NumberDecorator
{
    const TYPE = '04';

    public function __construct(NumberDecorator $numberObj)
    {
        $this->numberObj = $numberObj;
        static::$type = self::TYPE;
    }

}