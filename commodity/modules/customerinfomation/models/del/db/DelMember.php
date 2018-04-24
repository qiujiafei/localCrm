<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\del\db;

use common\ActiveRecord\MemberAR;
use Yii;

class DelMember extends MemberAR {

     //删除
    public static function delMember(array $condition) {

        return MemberAR::deleteAll($condition);
       
    }

}
