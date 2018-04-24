<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\interfaces;

/**
 * Interface check Purchase
 */
interface PurchaseCheckInterface
{
    /**
     * Check if a commodity information related a Purchase list.
     *
     * @param integer $commodityId
     * @return bool
     */
    public function hasPurchase($commodityId);
}
