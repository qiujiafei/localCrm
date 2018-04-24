<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class RbacRulesAR extends ActiveRecord
{
    public static function tableName() {
        return '{{%rbac_rules}}';
    }
}

