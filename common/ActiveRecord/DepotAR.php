<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:23
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

/**
 * Class DepotAR
 * @package common\ActiveRecord
 * 仓库管理父类
 */
class DepotAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%depot}}';
    }
}