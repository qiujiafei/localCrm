<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\del\db;

use common\ActiveRecord\ServiceAR;
use Yii;

class Del extends ServiceAR {

     //删除
    public static function delService(array $condition) {

        $del= ServiceAR::deleteAll($condition);
        if ($del<0) {
             throw new \Exception('服务项目删除失败', 9031);
            return false;
        }
        return true;
    }

}
