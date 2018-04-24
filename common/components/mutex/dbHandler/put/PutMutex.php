<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\mutex\dbHandler\put;

use common\ActiveRecord\SystemMutexAR;

class PutMutex extends SystemMutexAR
{
    public static function PutMutex($id, $table)
    {
        try {
            $mutex = new static;
            $mutex->row_id = $id;
            $mutex->row_table_name = $table;
            $mutex->timestamp = time();
            $mutex->save();
        } catch(\Exception $ex) {
            return false;
        }
        return true;
    }
}

