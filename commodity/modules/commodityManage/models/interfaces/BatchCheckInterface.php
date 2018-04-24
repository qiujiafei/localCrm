<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\interfaces;

/**
 * Interface check batch
 */
interface BatchCheckInterface
{
    /**
     * Get if batch stack count is zero.
     *
     * @param integer $commodityId
     * @return bool
     */
    public function getBatchIsZero($commodityId);
}
