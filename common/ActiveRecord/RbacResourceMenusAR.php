<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class RbacResourceMenusAR extends ActiveRecord
{
    public static function tableName() {
        return '{{%rbac_resource_menus}}';
    }
}

