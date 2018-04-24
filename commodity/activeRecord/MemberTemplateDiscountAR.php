<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberTemplateDiscountAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_template_discount}}';
    }
}
