<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\models\get\db;

use common\ActiveRecord\StoreAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends StoreAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        return self::find()->select([
                            'id',
                            'account',
                            'account_name',
                            'mobile',
                            'email',
                            'status'
                        ])->where([
                            'id' => $id,
                        ])->asArray()
                        ->one();
    }

    public static function getall($count_per_page, $page_num, $condition) {

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                        'id',
                        '',
                    ])
                    ->from('crm_testAccount')
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
    
}
