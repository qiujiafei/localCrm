<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace common\ActiveRecord;


use yii\db\ActiveRecord;

class ServiceAdditionAR extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%service_addition}}';
    }

}
