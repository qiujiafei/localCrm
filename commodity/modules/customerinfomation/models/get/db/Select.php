<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\get\db;

use common\ActiveRecord\CustomerInfomationAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends CustomerInfomationAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'a.id',
                            'a.customer_name',
                            'a.gender', //must modifed
                            'a.address',
                            'a.ID_number',
                            'a.birthday',
                            'a.cellphone_number',
                            'a.customer_origination',
                            'a.license_image_name',
                            'a.company',
                            'a.store_id',
                            'a.comment',
                            'a.status',
                            'a.created_by',
                            'a.created_time',
                            'a.status',
                            'a.created_time',
                            'b.card_number',
                        ])
                        ->from('crm_customer_infomation as a')
                        ->join('LEFT JOIN', 'crm_member As b', 'a.id = b.customer_infomation_id')
                        ->where([
                            'a.id' => $id,
                            'a.store_id' => current(self::getUser())->store_id,
                            'a.status' => 1,
                        ])
                        ->asArray()
                        ->one();
    }

    public static function getall($count_per_page, $page_num, $condition) {

        self::verifyStoreId();

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                        'a.id',
                        'a.customer_name',
                        'a.gender',
                        'a.cellphone_number',
                        'a.customer_origination',
                        'a.consume_count',
                        'a.total_consume_price',
                        'b.number_plate_number',
                        'b.number_plate_province_id',
                        'c.name as number_plate_province_name',
                        'b.number_plate_alphabet_id',
                        'd.name as number_plate_alphabet_name',
                        'b.number_plate_number',
                        'b.frame_number',
                        'f.name as alphabet_name',
                        'g.name as brand_name',
                        'b.model_id',
                        'e.name as style_name',
                        'e.year',
                        'b.insurance_expire',
                        'a.is_member',
                        'j.name as created_name',
                        'a.comment',
                        'a.created_time',
                        'a.last_modified_time',
                        'h.card_number',
                    ])
                    ->from('crm_customer_infomation as a')
                    ->join('LEFT JOIN', 'crm_customer_cars As b', 'a.id = b.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = b.number_plate_province_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = b.number_plate_alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = b.model_id')
                    ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                    ->join('LEFT JOIN', 'crm_member As h', 'a.id = h.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = a.created_by')
                    ->where($condition)
                    ->asArray(),
            'pagination' => [
                'page' => $page_num - 1,
                'pageSize' => $count_per_page,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_time' => SORT_DESC,
                ],
            ],
        ]);
    }

    public static function getexport($condition) {

        return  self::find()->select([
                        'a.id',
                        'a.customer_name',
                        'a.gender',
                        'a.cellphone_number',
                        'a.customer_origination',
                        'a.consume_count',
                        'a.total_consume_price',
                        'c.name as number_plate_province_name',
                        'd.name as number_plate_alphabet_name',
                        'b.number_plate_number',
                        'b.frame_number',
                        'f.name as alphabet_name',
                        'g.name as brand_name',
                        'e.name as style_name',
                        'e.year',
                        'b.insurance_expire',
                        'a.is_member',
                        'j.name as created_name',
                        'a.comment',
                        'a.created_time',
                    ])
                    ->from('crm_customer_infomation as a')
                    ->join('LEFT JOIN', 'crm_customer_cars As b', 'a.id = b.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = b.number_plate_province_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = b.number_plate_alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = b.model_id')
                    ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                    ->join('LEFT JOIN', 'crm_member As h', 'a.id = h.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = a.created_by')
                    ->where($condition)
                    ->asArray()
                    ->all();
    }

    public static function getonemember($id) {

        self::verifyStoreId();

        return self::find()->select([
                            'a.id',
                            'a.customer_name',
                            'a.cellphone_number',
                            'a.customer_origination',
                            'c.name as number_plate_province_name',
                            'd.name as number_plate_alphabet_name',
                            'b.frame_number',
                            'b.number_plate_number',
                            'f.name as alphabet_name',
                            'g.name as brand_name',
                            'e.name as style_name',
                            'e.year',
                            'h.card_number',
                            'h.type',
                            'h.price',
                            'h.comment',
                            'h.created_time',
                            'j.name as created_name',
                        ])
                        ->from('crm_customer_infomation  as a')
                        ->join('LEFT JOIN', 'crm_customer_cars As b', 'a.id = b.customer_infomation_id')
                        ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = b.number_plate_province_id')
                        ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = b.number_plate_alphabet_id')
                        ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = b.model_id')
                        ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                        ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                        ->join('LEFT JOIN', 'crm_member As h', 'a.id = h.customer_infomation_id')
                        ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = h.created_by')
                        ->where([
                            'a.id' => $id,
                            'a.store_id' => current(self::getUser())->store_id,
                            'a.status' => 1,
                        ])
                        ->asArray()
                        ->one();
    }

    public static function getallmember($count_per_page, $page_num, $condition) {

        self::verifyStoreId();

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                        'a.id',
                        'a.customer_name',
                        'a.cellphone_number',
                        'c.name as number_plate_province_name',
                        'd.name as number_plate_alphabet_name',
                        'b.number_plate_number',
                        'f.name as alphabet_name',
                        'g.name as brand_name',
                        'e.name as style_name',
                        'e.year',
                        'h.card_number',
                        'h.type',
                        'h.price',
                        'h.comment',
                        'h.created_time',
                        'j.name as created_name',
                    ])
                    ->from('crm_customer_infomation  as a')
                    ->join('LEFT JOIN', 'crm_customer_cars as b', 'a.id = b.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province as c', 'c.id = b.number_plate_province_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet as d', 'd.id = b.number_plate_alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_style_home as e', 'e.id = b.model_id')
                    ->join('LEFT JOIN', 'crm_car_alphabet_home as f', 'f.id = e.alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_brand_home as g', 'g.id = e.brand_id')
                    ->join('LEFT JOIN', 'crm_member as h', 'a.id = h.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = h.created_by')
                    ->where($condition)
                    ->asArray(),
            'pagination' => [
                'page' => $page_num - 1,
                'pageSize' => $count_per_page,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_time' => SORT_DESC,
                ],
            ],
        ]);
    }

    public static function getcount(array $condition = array()) {
        return new ActiveDataProvider([
            'query' => self::find()
                    ->from('crm_customer_infomation  as a')
                    ->join('LEFT JOIN', 'crm_member as b', 'a.id = b.customer_infomation_id')
                    ->where($condition)
                    ->count()
        ]);
    }

    public static function getmembercount(array $condition = array()) {
        
        return new ActiveDataProvider([
            'query' => self::find()
                    ->from('crm_customer_infomation  as b')
                    ->join('LEFT JOIN', 'crm_member as a', 'b.id = a.customer_infomation_id')
                    ->where($condition)
                    ->count()
        ]);
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

    public static function verifyStoreId() {

        $user = current(self::getUser());

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }
    }

}
