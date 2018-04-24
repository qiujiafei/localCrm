<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\put\db;

use common\ActiveRecord\CustomerCarsNumberPlateAlphabetAR;
use common\ActiveRecord\CustomerCarsNumberPlateProvinceAR;
use common\ActiveRecord\CarTypeHomeAR;
use common\ActiveRecord\CarBrandHomeAR;
use common\ActiveRecord\CarStyleHomeAR;


use Yii;

class getCarsNumber extends CustomerCarsNumberPlateAlphabetAR {

    //汽车信息字母
    public static function getAlphabet(array $condition, $field) {

        return CustomerCarsNumberPlateAlphabetAR::find()->select($field)->where($condition)->one();
        
    }

    //汽车信息省份
    public static function getProvince(array $condition, $field) {

        return CustomerCarsNumberPlateProvinceAR::find()->select($field)->where($condition)->one();
        
    }
    
    //汽车信息表型号
    public static function getCarTypeHome(array $condition, $field) {

        return CarTypeHomeAR::find()->select($field)->where($condition)->one();
        
    }
    
     //汽车信息表车款
    public static function getCarStyleHome(array $condition, $field) {

        return CarStyleHomeAR::find()->select($field)->where($condition)->one();
        
    }
    
     //汽车品牌
    public static function getCarBrandHome(array $condition, $field) {

        return CarBrandHomeAR::find()->select($field)->where($condition)->one();
        
    }

}
