<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberCardTypeAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_card_type}}';
    }
}
