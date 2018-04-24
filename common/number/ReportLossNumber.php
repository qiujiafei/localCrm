<?php
/**
 * Date: 2018/1/26
 * Time: 17:23
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 * @description  报损单单号生成
 */
namespace common\number;

class ReportLossNumber extends NumberDecorator
{
    const TYPE = '03';

    public function __construct(NumberDecorator $numberObj)
    {
        $this->numberObj = $numberObj;
        static::$type = self::TYPE;
    }

}