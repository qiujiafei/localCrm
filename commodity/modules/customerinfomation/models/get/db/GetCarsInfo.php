<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\get\db;

use common\ActiveRecord\CustomerInfomationCarsAR;
use yii\data\ActiveDataProvider;
use Yii;

class GetCarsInfo extends CustomerInfomationCarsAR {

    
    public static function getCarsCustomerInfomation($condition) {
        return self::find()->select([
                        'a.id',
                        'b.frame_number',
                        'b.number_plate_province_id',
                        'b.number_plate_alphabet_id',
                        'b.number_plate_number',
                        'b.model_id',
                        'b.vehicle_displacement',
                        'b.vehicle_price',
                        'b.engine_model',
                        'b.engine_number',
                        'b.manufacturer',
                        'b.leakage_status',
                        'b.vehicle_license_image_name',
//                'b.other_picture_id',
                        'b.next_service_mileage',
                        'b.prev_service_mileage',
                        'b.tire_status',
                        'b.color',
                        'b.break_status',
                        'b.break_oil_status',
                        'b.bettry_status',
                        'b.lubricating_oil_status',
                        'b.insurance_company',
                        'b.insurance_expire',
                        'b.fault_light',
                        'b.tire_brand_id',
                        'c.brand_name',
                        'b.tire_specification',
                    ])
                    ->from('crm_customer_infomation_cars as a')
                    ->join('LEFT JOIN', 'crm_customer_cars As b', 'a.customer_cars_id = b.id')
                    ->join('LEFT JOIN', 'crm_customer_cars_tire_brand As c', 'b.tire_brand_id = c.id')
                    ->where($condition)
                    ->asArray()
                    ->all();
    }

}
