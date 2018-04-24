<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class RbacUserRolesAR extends ActiveRecord
{
    public static function tableName() {
        return '{{%rbac_user_roles}}';
    }
}

