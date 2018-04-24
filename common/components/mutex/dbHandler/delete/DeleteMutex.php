<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\mutex\dbHandler\delete;

use common\ActiveRecord\SystemMutexAR;

class DeleteMutex extends SystemMutexAR
{
    public static function deleteExpiredMutex($id, $table, $timeout)
    {
        $expiredMutex = self::find()
            ->where(['row_id' => $id, 'row_table_name' => $table])
            ->andWhere(['<', 'timestamp', time()-$timeout])
            ->one();
        if($expiredMutex) {
            $expiredMutex->delete();
            return true;
        }
        return false;
    }

    public static function deleteMutex($id, $table)
    {
        $expiredMutex = self::find()
            ->where(['row_id' => $id, 'row_table_name' => $table])
            ->one();
        if($expiredMutex) {
            $expiredMutex->delete();
        }
        return true;
    }
}

