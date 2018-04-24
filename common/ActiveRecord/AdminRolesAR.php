<?php
/**
 * CRM system for 9daye
 *
 * @author: qzh <qianchaohui@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

/**
 * Class AdminRolesAR
 * @package common\ActiveRecord
 * 综合管理后台用户角色表
 */
class AdminRolesAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rbac_roles}}';
    }
}