<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\interfaces;

/**
 * Interface check inventory
 */
interface InventoryCheckInterface
{
    /**
     * Check if a commodity information related a Inventory list.
     *
     * @param integer $commodityId
     * @return bool
     */
    public function hasInventory($commodityId);
}
