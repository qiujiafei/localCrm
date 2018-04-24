<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\models\get\db;

use common\ActiveRecord\EmployeeAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends EmployeeAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($employee_number) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'store_id',
                            'name', //must modifed
                            'employee_number',
                            'employee_type_name',
                            'phone_number',
                            'qq_number',
                            'basic_salary',
                            'ID_card_image', //must modified
                            'ID_code',
                            'ability',
                            'attendance_code',
                            'comment',
                            'status',
                            'created_by',
                            'created_time',
//                            'last_modified_by',
//                            'last_modified_time',
                        ])
                        ->from('crm_employee')
                        ->where([
                            'employee_number' => $employee_number,
                            'store_id' => current(self::getUser())->store_id,
                        ])
                        ->asArray()
                        ->all();
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
                        'id',
                        'store_id',
                        'name', //must modifed
                        'employee_number',
                        'employee_type_name',
                        'phone_number',
                        'qq_number',
                        'basic_salary',
                        'ID_card_image', //must modified
                        'ID_code',
                        'ability',
                        'attendance_code',
                        'comment',
                        'status',
                        'created_by',
                        'created_time',
//                            'last_modified_by',
//                            'last_modified_time',
                    ])
                    ->from('crm_employee')
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
    
    public static function getemployee(array $condition = array(), $field = '*') {
        
        return EmployeeAR::find()->select($field)->where($condition)->asArray()->all();
        
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
