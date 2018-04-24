<?php
namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class EmployeeUserAR extends ActiveRecord
{
    public static function tableName()
    {
        return 'crm_employee_user';
    }
}