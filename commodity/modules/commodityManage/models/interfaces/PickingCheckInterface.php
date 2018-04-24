<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\interfaces;

/**
 * Interface check packing
 */
interface PickingCheckInterface
{
    /**
     * Check if a commodity information related a picking list.
     *
     * @param integer $commodityId
     * @return bool
     */
    public function hasPacking($commodityId);
}
