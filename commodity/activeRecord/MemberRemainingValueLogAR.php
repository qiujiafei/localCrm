<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberRemainingValueLogAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_remaining_value_log}}';
    }
}
