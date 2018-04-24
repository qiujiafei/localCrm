<?php
/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models;

use common\ActiveRecord\PurchaseAR;

class PurchaseLogicModel extends PurchaseAR
{
    //采购单状态 默认0(0:挂单 1:已结算 2:异常 3:作废)
    const PURCHASE_STATUS = [
        'zero' => 0,
        'one'  => 1,
        'two'  => 2,
        'three' => 3
    ];
    /**
     *  采购单单号是否存在
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
     * 是否可编辑，通过主键ID确认
     * @param $id
     * @return bool
     */
    public static function isEditableById($id)
    {
        $where = [
            'id' => $id,
            'status' => self::PURCHASE_STATUS['zero']
        ];
        return self::find()->where($where)->exists();
    }

    /**
     * 是否可编辑，通过单号标识确认
     * @param $number
     * @return bool
     */
    public static function isEditableByNumber($number)
    {
        $where = [
            'number' => $number,
            'status' => self::PURCHASE_STATUS['zero']
        ];
        return self::find()->where($where)->exists();
    }

    /**
     * 结算事件，回调函数，不可直接使用
     * @param $data
     * @param $reset  bool  重置计算，为真表示归零
     * @return mixed
     * @throws \Exception
     */

    public static function handleSettlement($data,$reset = true)
    {
        //优惠金额不可小于0
        if ($data['purchases']['discount'] < 0) {
            throw new \Exception('优惠金额不可小于0',12019);
            return false;
        }
        //实付金额不可小于0
        if ($data['purchases']['settlement_price'] < 0) {
            throw new \Exception('优惠金额不可小于0',12020);
            return false;
        }
        //原价
        $origin_price = true === $reset ? 0.00 : $data['purchases']['origin_price'];
        //优惠价
        $discount = $data['purchases']['discount'] ?? 0.00;
        $price = true === $reset ? 0.00 : $data['purchases']['settlement_price'];
        foreach ($data['commodities'] as $k=>$commodity) {
            if ($commodity['current_price'] < 0) {
                throw new \Exception('单价不可小于0',12021);
            }
            $price = floatval($commodity['current_price']) * floatval($commodity['quantity']);
            //总价累计计算
            $origin_price += $price;
            //当前单品总价
            $data['commodities'][$k]['total_price'] = $price;
        }
        //结算价格
        $settlement_price = $origin_price - $discount;
        $data['purchases']['origin_price'] = round($origin_price,2);
        $data['purchases']['settlement_price'] = $settlement_price < 0.00 ? 0.00 : round($settlement_price,2);
        return $data;
    }

    /**
     * 返回格式化的数组
     * commodities为提交的商品列表
     * purchases 为采购单信息
     * array(
     *     ['commodities'] => array(
     *         [0] => array(),
     *         [1] => array(),
     *         [2] => array().

     *     ),
     *     ['purchases'] => array(
     *         ['supplier_id'] =>
     *         ['status']      =>
     *         ['comment']     =>
     *         ['purchase_by'] =>
     *         ['origin_price']=>
     *         ['discount']    =>
     *         ['settlement_price'] =>
     *     )

     * )
     *
     * @param $data
     * @return array
     */
    public static function resolvePostData($data)
    {
        $return  = $purchases = [];
        array_walk($data,function ($v,$k) use (&$purchases){
            if ( ! is_array($v)) {
                $purchases[$k] = $v;
            }
        });
        $return['purchases'] = $purchases;
        $return['commodities'] = $data['commodities'];
        return $return;
    }

    /**
     * 获取实际的支付金额
     * @param $storeId
     * @return mixed
     */
    public static function getSettlementPrice($storeId)
    {
        $where = ['store_id' => $storeId];
        return self::find()->where($where)->sum('settlement_price') ?? '0.00';
    }
}