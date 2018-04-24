<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/5
 * Time: 12:00
 */

namespace common\validators\item;


use common\ActiveRecord\OrderRefundAR;
use common\models\parts\ItemInOrder;
use common\models\parts\Order;
use common\models\parts\order\OrderRefund;
use common\models\Validator;
use yii\base\Exception;

class RefundItemIdValidator extends Validator
{

    public $message;
    public $messageForOrder;
    public $messageForUser;
    public $messageForQuantity;//
    public $messageRefundExists;//退货订单已存在
    public $validatorExists;
    public $messageForStatus;//验证订单状态失败时返回

    public $order_code;//订单ID
    public $custom_user_id;//用户Id
    public $quantity;//数量

    public function validateValue($item_id)
    {
        try {
            $item = new ItemInOrder(['id' => $item_id]);

            //验证订单需是否属于订单
            if ($this->order_code) {
                if ($item->getOrder()->getOrderNo() != $this->order_code) {
                    return $this->messageForOrder;
                }
            }
            //验证订单是否属于用户
            if ($this->custom_user_id) {
                if ($item->getCustomerId() != $this->custom_user_id) {
                    return $this->messageForUser;
                }
            }
            //已退款数量
            $refund_quantity=0;
            //验证退换货订单是否存在
            if ($this->validatorExists) {
                $where="order_item_id='$item->id' and (status!='".OrderRefund::REFUND_STATUS_REJECT."' and status!='".OrderRefund::REFUND_STATUS_REFUND_MONEY."' and status!='".OrderRefund::REFUND_STATUS_FINISHED."')";
                if (OrderRefundAR::find()->where($where)->exists()) {
                    return $this->messageRefundExists;
                }

                //
                $where="order_item_id='".$item->id."'";
                $refund_quantity=OrderRefundAR::find()->where($where)->sum('quantity');

            }

            //验证订购数量
            if ($this->quantity > 0) {
                if ($item->getCount() < $this->quantity+(int)$refund_quantity) {
                    return $this->messageForQuantity;
                }
            }
            //验证状态
             if($item->getOrder()->getStatus()!=Order::STATUS_CONFIRMED){
                return $this->messageForStatus;
             }

            return true;
        } catch (Exception $e) {
 
            //出错,提示必选参数
            return $this->message;
        }
    }

}