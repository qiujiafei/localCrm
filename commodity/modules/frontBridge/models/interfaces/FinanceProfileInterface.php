<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\frontBridge\models\interfaces;

/**
 * Bridge for front end API of finance.
 */
interface FinanceProfileInterface
{
    /**
     * Get Today's finace data of current login user.
     *
     * @return number
     */
    public function getToady();
}
