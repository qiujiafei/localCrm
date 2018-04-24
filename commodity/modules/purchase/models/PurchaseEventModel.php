<?php
/**
 * CRM system for 9daye
 * 采购单相关事件，根据status进行不同的改变
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\models;

use common\ActiveRecord\PurchaseAR;

class PurchaseEventModel extends PurchaseAR
{
    //采购单挂单事件
    const EVENT_PURCHASE_ZERO = 'purchase_zero';
    //采购单结算事件
    const EVENT_PURCHASE_ONE = 'purchase_one';
    //采购单异常事件
    const EVENT_PURCHASE_TWO = 'purchase_two';
    //采购单作废事件
    const EVENT_PURCHASE_THREE = 'purchase_three';
}