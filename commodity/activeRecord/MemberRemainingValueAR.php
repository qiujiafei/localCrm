<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberRemainingValueAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_remaining_value}}';
    }
}
