<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\get\db;

use common\ActiveRecord\DamagedAR;
use common\ActiveRecord\DamagedCommodityAR;
use yii\data\ActiveDataProvider;
use commodity\modules\damagedcommodity\models\get\db\Select as damagedcommodity_select;
use Yii;

class Select extends DamagedAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;
    
    //获取报损商品详情
    public static function getone($id) {
        
        return damagedcommodity_select::getone($id);
        
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
                        'a.number',
                        'b.id as damaged_id_main',
                        'b.name as damaged_by_name',
                        'a.created_time',
                        'c.name as store_name',
                        'a.comment',
                    ])
                    ->from('crm_damaged as a')
                    ->join('LEFT JOIN', 'crm_employee_user As b', 'b.id = a.damaged_by')
                    ->join('LEFT JOIN', 'crm_store As c', 'c.id = a.store_id')
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

    public static function getCount(array $condition = array()) {

        return DamagedAR::find()->where($condition)->count();
    }

    public static function getSumMoney($field, $condition) {
        return new ActiveDataProvider([
            'query' => self::find()
                    ->from('crm_damaged as a')
                    ->join('LEFT JOIN', 'crm_damaged_commodity As b', 'a.id =b.damaged_id')
                    ->where($condition)
                    ->sum($field),
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
