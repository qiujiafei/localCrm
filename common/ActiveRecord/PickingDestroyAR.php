<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace common\ActiveRecord;


use yii\db\ActiveRecord;

class PickingDestroyAR extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%picking_destroy}}';
    }

}
