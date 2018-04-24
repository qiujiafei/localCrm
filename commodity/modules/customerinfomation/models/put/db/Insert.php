<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\put\db;

use common\ActiveRecord\CustomerInfomationAR;
use commodity\modules\customerinfomation\models\put\db\InsertCustomerInfomationCars;
use commodity\modules\customerinfomation\models\put\db\InsertCustomerCars;
use Yii;

class Insert extends CustomerInfomationAR {

    //添加
    public static function insertCustomerInfomation(array $data) {
      
        $car_info_array = $data['car_info'];
        unset($data['car_info']);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }

            if ($Insert->save(false)) {
                $customer_infomation_cars['customer_infomation_id'] = $customer_cars['customer_infomation_id'] = $Insert->id;
                $customer_infomation_cars['last_modified_by'] = $customer_cars['last_modified_by'] = $data['last_modified_by'];
                $customer_infomation_cars['last_modified_time'] = $customer_cars['last_modified_time'] = $data['last_modified_time'];
                $customer_infomation_cars['created_time'] = $customer_cars['created_time'] = $data['created_time'];
                $customer_infomation_cars['created_by'] = $customer_cars['created_by'] = $data['created_by'];
                $customer_infomation_cars['store_id'] = $customer_cars['store_id'] = $data['store_id'];

                foreach ($car_info_array as $key => $value) {
                    foreach ($value as $field => $val) {
                        $customer_cars[$field] = $val;
                    }
                    //根据车牌判断是否存在同样的车
                    $customer_cars_condition['number_plate_province_id'] = $value['number_plate_province_id'];
                    $customer_cars_condition['number_plate_alphabet_id'] = $value['number_plate_alphabet_id'];
                    $customer_cars_condition['number_plate_number'] = $value['number_plate_number'];
                    $customer_cars_condition['store_id'] = $data['store_id'];
                    
                    if (InsertCustomerCars::getField($customer_cars_condition,'id')) {
                         throw new \Exception('客户汽车信息重复', 17037);
                    }
                    
                    $customer_cars_id = InsertCustomerCars::insertData($customer_cars);
                    
                    if ($customer_cars_id) {
                        $customer_infomation_cars['customer_cars_id'] = $customer_cars_id;
                        $customer_infomation_cars_model = InsertCustomerInfomationCars::insertData($customer_infomation_cars);
                        
                        if (!$customer_infomation_cars_model) {
                            $transaction->rollback();
                            return false;
                        }
                    } else {
                        $transaction->rollback();
                        return false;
                    }
                }
            } else {
                $transaction->rollback();
                return false;
            }
            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return CustomerInfomationAR::find()->select($field)->where($condition)->one();
    }
    
    //获取所有
    public static function getall(array $condition, $field) {

        return CustomerInfomationAR::find()->select($field)->where($condition)->asArray()->all();
    }

}
