<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 10:54
 * 供应商AR
 */

namespace common\ActiveRecord;


use yii\db\ActiveRecord;

class SupplierAR extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%supplier}}';
    }

}