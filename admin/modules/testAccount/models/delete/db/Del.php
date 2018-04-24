<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\models\delete\db;

use common\ActiveRecord\StoreAR;
use Yii;

class Del extends StoreAR {

    //删除
    public static function delStore(array $condition) {

        $del = StoreAR::deleteAll($condition);
        
        if ($del < 0) {
            throw new \Exception('账号删除失败', 1015);
            return false;
        }
        
        return true;
    }

}
