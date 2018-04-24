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
interface MemberPointInterface
{
    /**
     * get member point rate by storeId.
     *
     * @param integer $storeId
     * @return string
     */
    public function getPointRate($storeId);
}
