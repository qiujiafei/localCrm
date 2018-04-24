<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\get\db;

use common\ActiveRecord\ServiceAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends ServiceAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone() {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'id',
                            'service_name',
                            'specification',
                            'service_code',
                            'price',
                            'type',
                            'store_id',
//                            'service_claasification_id ',
                            'service_claasification_name',
                            'status',
                            'comment',
                            'created_by',
                            'created_time',
//                            'last_modified_by',
//                            'last_modified_time'
                        ])
                        ->from('crm_service')
                        ->where([
                            'id' => $id,
                            'store_id' => current(self::getUser())->store_id,
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
                        'id',
                        'service_name',
                        'specification',
                        'service_code',
                        'price',
                        'type',
                        'store_id',
                        'service_claasification_id',
                        'service_claasification_name',
                        'status',
                        'comment',
                        'created_by',
                        'created_time',
//                            'last_modified_by',
//                            'last_modified_time'
                    ])
                    ->from('crm_service')
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

        $user = current(self::getUser());

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }

        return self::find()
                        ->select([
//                            'id',
                            'service_name',
                            'specification',
                            'service_code',
                            'price',
                            'type',
                            'store_id',
                            'service_claasification_name',
                            'status',
                            'comment',
                            'created_by',
                            'created_time',
                        ])
                        ->from('crm_service')
                        ->where($condition)
                        ->asArray()
                        ->all();
    }

    public static function verifyStoreId() {

        $user = current(self::getUser());

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

}
