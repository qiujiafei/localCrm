<?php

namespace common\ActiveRecord;


use yii\db\ActiveRecord;

class SystemMutexAR extends ActiveRecord
{

    public static function tableName()
    {
        return '{{system_mutex}}';
    }

}
