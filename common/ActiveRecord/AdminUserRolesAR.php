<?php
/**
 * CRM system for 9daye
 *
 * @author: qzh <qianchaohui@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

/**
 * Class AdminUserRolesAR
 * @package common\ActiveRecord
 * 综合管理后台用户角色关系表
 */
class AdminUserRolesAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rbac_user_roles}}';
    }
}