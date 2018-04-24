<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberRemainingTimesLogAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_remaining_times_log}}';
    }
}
