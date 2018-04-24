<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\put\db;

use common\ActiveRecord\CustomerCarsAR;
use Yii;

class InsertCustomerCars extends CustomerCarsAR {

    //添加
    public static function insertData(array $data) {
        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }

            $Insert->save(false);
            
            return $Insert->id;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
     //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return CustomerCarsAR::find()->select($field)->where($condition)->one();
    }

}
