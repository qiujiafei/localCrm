<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\models\get\db;

use common\ActiveRecord\UnitAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends UnitAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'id',
                            'unit_name',
                            'store_id',
                            'created_time',
                        ])
                        ->from('crm_unit')
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
                        'unit_name',
                        'store_id',
                        'created_time',
                    ])
                    ->from('crm_unit')
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
