<?php
/**
 * CRM system for 9daye
 * Date: 2018/2/3
 * Time: 10:10
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\inventory\logics;

use common\ActiveRecord\InventoryAR;

class InventoryLogicModel extends InventoryAR
{
    /**
     * 允许的status值，0为正常，1为作废
     */
    const ALLOW_STATUS_VALUE = [
        'zero' => 0,
        'one' => 1
    ];


    /**
     *  盘点单单号是否存在
     * @param $number
     * @return bool
     */
    public static function isExistsByNumber($number)
    {
        $where = [
            'number' => $number
        ];
        return self::find()->where($where)->exists();
    }

    /**
     * 返回格式化的数组
     * commodities 为被盘点的商品列表
     * inventories 为盘点单信息
     * array(
     *     ['commodities'] => array(
     *         [0] => array(),
     *         [1] => array(),
     *         [2] => array().

     *     ),
     *     ['inventories'] => array(
     *         ['number'] =>
     *         ['status']      =>
     *         ['comment']     =>
     *         ['inventory_id'] =>
     *         ['store_id']=>
     *     )

     * )
     *
     * @param $data
     * @return array
     */
    public static function resolvePostData($data)
    {
        $purchases = [];
        array_walk($data,function ($v,$k) use (&$purchases,&$data){
            if ( ! is_array($v)) {
                $purchases[$k] = $v;
                unset($data[$k]);
            }
        });
        $data['inventories'] = $purchases;
        return $data;
    }

    /**
     * 格式化提交的数据，使其填充必填数据
     * @param $data 提交的原始数据
     * @param array $otherFields   其他必填字段
     * @return array
     * @throws \Exception
     */
    public static function formatAddData($data,$otherFields=[])
    {
        if ( ! is_array($data) || count($data) < 1) {
            throw new \Exception('盘点单中无商品，不可添加',17004);
        }

        if (count($otherFields) > 0) {
            return array_map(function ($v) use ($otherFields) {
                $non_replaceable = [];
                foreach ($v as $k2=>$v2) {
                    if ( isset($v[$k2]) && isset($otherFields[$k2])  ) {
                        $non_replaceable[$k2] = $v2;
                    }
                }

                return array_merge($v,$otherFields,$non_replaceable);
            },$data);
        }
        return $data;
    }


}