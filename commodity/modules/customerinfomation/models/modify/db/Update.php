<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\modify\db;

use common\ActiveRecord\CustomerInfomationAR;
use commodity\modules\customerinfomation\models\modify\db\UpdateCustomerCars;
use commodity\modules\customerinfomation\models\put\db\InsertCustomerCars;
use commodity\modules\customerinfomation\models\put\db\InsertMember;
use commodity\modules\customerinfomation\models\del\db\DelMember;
use Yii;

class Update extends CustomerInfomationAR {

    //更改
    public static function modifyCustomerInfomation(array $condition, array $modify_data) {
        $car_info_array = $modify_data['car_info'];
        unset($modify_data['car_info']);
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $modify = CustomerInfomationAR::find()->where($condition)->one();

            if (!$modify) {
                $transaction->rollback();
                return false;
            }

            foreach ($modify_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                $transaction->rollback();
                return false;
            } else {

                foreach ($car_info_array as $key => $value) {
                    $car_info_condition['id'] = $id = $value['id'];
                    $car_info = InsertCustomerCars::getField($car_info_condition, 'number_plate_province_id,number_plate_alphabet_id,number_plate_number');

                    if ($car_info) {
                        if (!$value['number_plate_province_id']) {
                            $value['number_plate_province_id'] = $car_info->number_plate_province_id;
                        } elseif (!$value['number_plate_alphabet_id']) {
                            $value['number_plate_alphabet_id'] = $car_info->number_plate_alphabet_id;
                        } elseif (!$value['number_plate_number']) {
                            $value['number_plate_number'] = $car_info->number_plate_number;
                        }
                        $car_repetition_condition['number_plate_province_id'] = $value['number_plate_province_id'];
                        $car_repetition_condition['number_plate_alphabet_id'] = $value['number_plate_alphabet_id'];
                        $car_repetition_condition['number_plate_number'] = $value['number_plate_number'];
                        $car_repetition_condition['store_id'] = $modify_data['store_id'];
                        $car_repetition_info = InsertCustomerCars::getField($car_repetition_condition, 'id');
                        if ($car_repetition_info) {
                            if ($car_repetition_info->id != $id) {
                                throw new \Exception('客户汽车信息重复', 17037);
                            }
                        }

                        unset($car_info_array[$key]['id']);

                        $modify_car = UpdateCustomerCars::modifyCustomerCars($car_info_condition, $car_info_array[$key]);
                        if ($modify_car === false) {
                            $transaction->rollback();
                            return false;
                        }
                    } else {
                        throw new \Exception('用户车辆信息有误', 17039);
                    }
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //变成会员
    public static function changeMember(array $condition, array $add_data) { 
        $modify_customer_data['is_member'] = 1;
        $transaction = Yii::$app->db->beginTransaction();
        try {

             $modify = CustomerInfomationAR::find()->where($condition)->one();

            if (!$modify) {
                $transaction->rollback();
                return false;
            }
            
            foreach ($modify_customer_data as $k => $v) {
                $modify->$k = $v;
            }
            
            if ($modify->save()=== false) {
                $transaction->rollback();
                return false;
            } else {
                
                if (InsertMember::InsertMemberData($add_data) === false) {
                    $transaction->rollback();
                    return false;
                }
            }

            $transaction->commit();
            return true;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //删除会员
    public static function delMember(array $condition) { 
        $modify_customer_data['is_member'] = 0;
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $modify = CustomerInfomationAR::updateAll($modify_customer_data, $condition);

          
            if ($modify === false) {
                $transaction->rollback();
                return false;
            } else {
                $member_condition['customer_infomation_id']=$condition['id'];
                
                if (DelMember::delMember($member_condition) === false) {
                    $transaction->rollback();
                    return false;
                }
            }

            $transaction->commit();
            return true;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    //批量更改
    public static function modifyAllCustomerInfomation(array $condition, array $modify_data) {

        try {

            return CustomerInfomationAR::updateAll($modify_data, $condition);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * 更改消费次数
     */
    public static function modifyConsumeTimes(array $condition, array $modify_data) {
        try {
            $modify = CustomerInfomationAR::find()->where($condition)->one();
            if (!$modify) {
                return false;
            }
            return $modify->updateCounters($modify_data); //updateCounters 实现 减法 值：给负数  ['num'=>"-$value[shop_num]"]
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
