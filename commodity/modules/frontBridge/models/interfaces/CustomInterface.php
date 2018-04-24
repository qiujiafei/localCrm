<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\frontBridge\models\interfaces;

/**
 * Bridge for front end API of Custom infomation.
 */
interface CustomInterface
{
    /**
     * Custom count from bill.
     *
     * @return array
     */
    public function customFromBill();

    /**
     * Member count
     *
     * @return number
     */
    public function memberCount();

    /**
     * Member increse of this month
     *
     * @return number
     */
    public function monthMemberIncrese();
}
