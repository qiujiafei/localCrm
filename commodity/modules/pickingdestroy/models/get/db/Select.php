<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingdestroy\models\get\db;

use common\ActiveRecord\PickingDestroyAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends PickingDestroyAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($pickingdestroy_number) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'store_id',
                            'name', //must modifed
                            'pickingdestroy_number',
                            'pickingdestroy_type_name',
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
                        ->from('crm_pickingdestroy')
                        ->where([
                            'pickingdestroy_number' => $pickingdestroy_number,
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
                        'a.id',
                        'a.picking_number',
                        'a.total_quantity',
                        'a.total_price',
                        'b.name as picking_name',
                        'a.created_time',
                        'a.comment',
                        'c.name as store_name',
                        'a.comment',
                    ])
                    ->from('crm_picking_destroy as a')
                    ->join('LEFT JOIN', 'crm_employee_user As b', 'a.picking_by = b.id')
                    ->join('LEFT JOIN', 'crm_store As c', 'a.store_id = c.id')
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
