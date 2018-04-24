<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\store\models\get\db;

use common\ActiveRecord\StoreAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends StoreAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getAccredit($count_per_page, $page_num, $condition) {

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                        'a.id',
                        'a.account',
                        'b.property_id',
                        'a.mobile',
                        'a.created_time',
                        'a.last_login_time',
                        'a.ip_address',
                        'a.ip_number',
                    ])
                    ->from('crm_employee_user as a')
                    ->join('LEFT JOIN', 'crm_store As b', 'a.store_id = b.id')
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

    public static function getForbidden($count_per_page, $page_num, $condition) {

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                        'a.id',
                        'a.account',
                        'b.property_id',
                        'a.mobile',
                        'a.created_time',
                        'a.last_login_time',
                        'a.last_modified_time',
                        'a.comment',
                    ])
                    ->from('crm_employee_user as a')
                    ->join('LEFT JOIN', 'crm_store As b', 'a.store_id = b.id')
                    ->where($condition)
                    ->asArray(),
            'pagination' => [
                'page' => $page_num - 1,
                'pageSize' => $count_per_page,
            ],
            'sort' => [
                'defaultOrder' => [
                    'last_modified_time' => SORT_DESC,
                ],
            ],
        ]);
    }

}
