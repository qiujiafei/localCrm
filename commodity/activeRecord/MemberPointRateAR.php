<?php

/**
 * CRM system for 9daye
 *
 * @author: wj <wangjie@9daye.com.cn>
 */

namespace commodity\activeRecord;

use yii\db\ActiveRecord;

class MemberPointRateAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member_point_rate}}';
    }
}
