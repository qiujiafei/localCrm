<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damagedcommodity\models\get\db;

use common\ActiveRecord\DamagedCommodityAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends DamagedCommodityAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {
 
        self::verifyStoreId();

        return self::find()
                        ->select([
                            'b.commodity_name',
                            'a.cost_price',
                            'a.quantity',
                            'a.total_price',
                            'c.name as store_name',
                        ])
                        ->from('crm_damaged_commodity as a')
                        ->join('LEFT JOIN', 'crm_commodity As b', 'a.commodity_id = b.id')
                        ->join('LEFT JOIN', 'crm_store As c', 'c.id = a.store_id')
                        ->where([
                            'a.damaged_id' => $id,
                            'a.store_id' => current(self::getUser())->store_id,
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
                        'a.id',
                        'b.number',
                        'c.commodity_name',
                        'c.specification',
                        'c.commodity_code',
                        'a.quantity',
                        'a.cost_price',
                        'a.total_price',
                        'd.name as damaged_by_name',
                        'e.name as store_name',
                        'g.depot_name as default_depot_name',
                        'a.comment',
                        'a.created_time',
                    ])
                    ->from('crm_damaged_commodity as a')
                    ->join('LEFT JOIN', 'crm_damaged As b', 'a.damaged_id = b.id')
                    ->join('LEFT JOIN', 'crm_commodity As c', 'a.commodity_id = c.id')
                    ->join('LEFT JOIN', 'crm_employee_user As d', 'b.damaged_by = d.id')
                     ->join('LEFT JOIN', 'crm_store As e', 'c.id = a.store_id')
                    ->join('LEFT JOIN', 'crm_commodity_batch As f', 'f.id =a.commodity_batch_id')
                    ->join('LEFT JOIN', 'crm_depot As g', 'g.id = f.depot_id')
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
    

    public static function getProAll(array $condition = array(), $field = '*') {

        return DamagedCommodityAR::find()->select($field)->where($condition)->asArray()->all();
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
