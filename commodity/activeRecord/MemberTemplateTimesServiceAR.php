<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberTemplateTimesServiceAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_template_times_service}}';
    }
}
