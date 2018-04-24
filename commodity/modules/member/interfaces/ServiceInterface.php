<?php

/** 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */
namespace commodity\modules\member\models\interfaces;

/**
 * Interface get service list
 */
interface ServiceInterface
{
    /**
     * Get if batch stack count is zero.
     *
     * @param integer $store_id
     * @return bool
     */
    public function getServiceList($store_id);
}
