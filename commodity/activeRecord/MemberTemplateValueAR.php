<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberTemplateValueAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_template_value}}';
    }
}
