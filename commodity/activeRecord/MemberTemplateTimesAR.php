<?php

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberTemplateTimesAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_template_times}}';
    }
}
