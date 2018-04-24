<?php
namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class ServiceClassificationAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%service_classification}}';
    }
}