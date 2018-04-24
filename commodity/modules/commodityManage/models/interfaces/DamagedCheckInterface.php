<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\interfaces;

/**
 * Interface check damaged
 */
interface DamagedCheckInterface
{
    /**
     * Check if a commodity information related a damaged list.
     *
     * @param integer $commodityId
     * @return bool
     */
    public function hasDamaged($commodityId);
}
