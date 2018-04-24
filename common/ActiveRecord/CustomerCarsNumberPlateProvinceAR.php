<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class CustomerCarsNumberPlateProvinceAR extends ActiveRecord
{
    public static function tableName() {
        return '{{%customer_cars_number_plate_province}}';
    }
}

