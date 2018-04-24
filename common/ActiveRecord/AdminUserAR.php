<?php
/*  *
 * CRM system for 9daye
 *
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

/**
 * Class AdminUserAR
 * @package common\ActiveRecord
 * 综合管理后台用户表
 */
class AdminUserAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user}}';
    }
}