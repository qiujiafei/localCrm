<?php
/**
 * Date: 2018/2/1
 * Time: 11:20
 * 单号生成工厂
 * @author hejinsong@9daye.com.cn
 */

namespace common\number;

class NumbersFactory
{
    /**
     * 可允许操作的单号，每增加一个，此处就必须增加一个
     */
    const TYPE_LIST_NUMBER = [
        PurchaseNumber::class,
        PickingNumber::class,
        ReportLossNumber::class,
        RefundOfGoodsNumber::class,
        InventoryNumber::class,
        AllocationNumber::class,
        CostNumber::class
    ];

    /**
     * @param array $config
     * @return string
     */
    public static function generationNumber(array $config=[]) : string
    {
        $typeClass = self::handleType($config['type']);
        $number = new $typeClass(new NumberDecorator($config['origin']));
        return $number->getNumber();
    }

    /**
     * @param $type
     * @return mixed|string
     */
    protected static function handleType($type)
    {
        $classString = '';
        foreach (self::TYPE_LIST_NUMBER as $class)
        {
            if ($class::TYPE == $type){
                $classString = $class;
                break;
            }
        }
        return $classString;
    }
}