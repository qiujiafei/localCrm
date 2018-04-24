<?php
/**
 * Date: 2018/1/26
 * Time: 17:20
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description  领料单单号生成
 */
namespace common\number;

class PickingNumber extends NumberDecorator
{
    const TYPE = '02';

    public function __construct(NumberDecorator $numberObj)
    {
        $this->numberObj = $numberObj;
        static::$type = self::TYPE;
    }

}