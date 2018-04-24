<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberRemainingTimesAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_remaining_times}}';
    }
}
