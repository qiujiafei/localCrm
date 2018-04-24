<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberRemainingDiscountAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_remaining_discount}}';
    }
}
