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
 * Class StoreAR
 * @package common\ActiveRecord
 * 门店管理
 */
class StoreAR extends ActiveRecord
{
    public static function tableName()
    {
        return 'crm_store';
    }
}