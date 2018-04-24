<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\carbasicinformation\models\get\db;

use common\ActiveRecord\CarTypeHomeAR;
use common\ActiveRecord\CustomerCarsTireBrandAR;
use common\ActiveRecord\CustomerCarsNumberPlateAlphabetAR;
use common\ActiveRecord\CustomerCarsNumberPlateProvinceAR;
use common\ActiveRecord\CarAlphabetHomeAR;
use common\ActiveRecord\CarBrandHomeAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends CustomerCarsTireBrandAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'id',
                            'brand_name',
                        ])
                        ->from('crm_customer_cars_tire_brand')
                        ->where([
                            'id' => $id,
                            'store_id' => current(self::getUser())->store_id,
                        ])
                        ->asArray()
                        ->one();
    }

    public static function getall(array $condition = array(), $field = '*') {

        return CustomerCarsTireBrandAR::find()->select($field)->where($condition)->asArray()->all();
    }

    public static function getCarNumberAlphabet(array $condition = array(), $field = '*') {

        return CustomerCarsNumberPlateAlphabetAR::find()->select($field)->where($condition)->asArray()->all();
    }

    public static function getCarNumberProvince(array $condition = array(), $field = '*') {

        return CustomerCarsNumberPlateProvinceAR::find()->select($field)->where($condition)->asArray()->all();
    }

    public static function getCarAlphabetHome(array $condition = array(), $field = '*') {

        return CarAlphabetHomeAR::find()->select($field)->where($condition)->asArray()->all();
    }

    public static function getCarBrandHome(array $condition = array()) {

        return self::find()->select([
                            'a.id',
                            'a.alphabet_id',
                            'a.name as brand_name',
                            'b.name as alphabet_name',
                        ])
                        ->from('crm_car_brand_home as a')
                        ->join('LEFT JOIN', 'crm_car_alphabet_home As b', 'a.alphabet_id = b.id')
                        ->where($condition)
                        ->asArray()
                        ->all();
    }

    public static function getCarTypeHome(array $condition = array()) {
        return self::find()->select([
                            'a.id',
                            'a.alphabet_id',
                            'b.name as alphabet_name',
                            'a.brand_id',
                            'c.name as brand_name',
                            'a.vender_id',
                            'c.name as vender_name',
                            'a.name as car_type_name',
                        ])
                        ->from('crm_car_type_home as a')
                        ->join('LEFT JOIN', 'crm_car_alphabet_home As b', 'a.alphabet_id = b.id')
                        ->join('LEFT JOIN', 'crm_car_brand_home As c', 'a.brand_id = c.id')
                        ->join('LEFT JOIN', 'crm_car_vender_home As d', 'a.vender_id = d.id')
                        ->where($condition)
                        ->asArray()
                        ->all();
    }

    public static function getCarStyleHome(array $condition = array()) {

        return self::find()->select([
                            'a.id',
                            'a.alphabet_id',
                            'b.name as alphabet_name',
                            'a.brand_id',
                            'c.name as brand_name',
                            'a.vender_id',
                            'c.name as vender_name',
                            'a.type_id',
                            'e.name as car_type_name',
                            'a.year',
                            'a.name as style_name',
                        ])
                        ->from('crm_car_style_home as a')
                        ->join('LEFT JOIN', 'crm_car_alphabet_home As b', 'a.alphabet_id = b.id')
                        ->join('LEFT JOIN', 'crm_car_brand_home As c', 'a.brand_id = c.id')
                        ->join('LEFT JOIN', 'crm_car_vender_home As d', 'a.vender_id = d.id')
                        ->join('LEFT JOIN', 'crm_car_type_home As e', 'a.type_id = e.id')
                        ->where($condition)
                        ->asArray()
                        ->all();
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
