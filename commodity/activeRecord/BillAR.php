<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class BillAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%bill}}';
    }
}
