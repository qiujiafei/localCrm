<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\models\modify\db;

use common\ActiveRecord\PurchaseAR;

class UpdateModel extends PurchaseAR
{
    /**
     * 更新状态
     * @param $where
     * @param $status
     * @return int
     */
    public function updateStatusById($where,$status)
    {
        return self::updateAll(['status'=>$status],$where);
    }
}