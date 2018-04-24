<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\get\db;

use common\ActiveRecord\EmployeeUserAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends EmployeeUserAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'id',
                            'account',
                            'account_name',
                            'employee_id',
                            'name',
                            'status',
                            'created_time',
                            'last_modified_time',
                        ])
                        ->from('crm_employee_user')
                        ->where([
                            'id' => $id,
                            'store_id' => current(self::getUser())->store_id,
                        ])
                        ->asArray()
                        ->all();
    }

    public static function getpart($count_per_page, $page_num, $condition) {

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
                        'account',
                        'account_name',
                        'employee_id',
                        'name',
                        'status',
                        'created_time',
                        'last_modified_time',
                    ])
                    ->from('crm_employee_user')
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

    public static function getall($condition) {
        return self::find()->select([
                        'a.id',
                        'a.account',
                        'a.account_name',
                        'a.name',
                        'a.status',
                        'a.created_time',
                        'a.last_modified_time',
                        'a.employee_id',
                        'a.store_id',
                        'b.employee_type_name',
                    ])
                    ->from('crm_employee_user AS a')
                    ->leftJoin('crm_employee AS b', 'a.employee_id = b.id')
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
