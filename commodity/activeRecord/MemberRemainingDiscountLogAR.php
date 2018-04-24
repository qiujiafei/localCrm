<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberRemainingDiscountLogAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_remaining_discount_log}}';
    }
}
