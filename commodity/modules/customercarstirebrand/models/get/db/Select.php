<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\get\db;

use common\ActiveRecord\CustomerCarsTireBrandAR;
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
