<?php
/**
 * Date: 2018/1/26
 * Time: 17:27
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description  成本调整单单号生成
 */
namespace common\number;

class CostNumber extends NumberDecorator
{
    const TYPE = '07';

    public function __construct(NumberDecorator $numberObj)
    {
        $this->numberObj = $numberObj;
        static::$type = self::TYPE;
    }

}