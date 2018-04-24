<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:20
 */

namespace commodity\modules\store\models\get\db;
use commodity\modules\store\models\StoreObject;
use common\ActiveRecord\StoreAR;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class SelectModel extends StoreAR
{
    
    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;
    
    /**
     * @param array $where
     * @param int $pageSize
     * @return array
     * 查找列表
     */
    public function findList($where=[],$pageSize=20)
    {
        $storeObject = new StoreObject();
        $query = $storeObject->findListQuery($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $list =  $storeObject->findListQueryOfPagination($query,['name','phone_number','address','is_main_store'],$pagination->offset,$pagination->limit);

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];

    }
    
    
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
                        'a.ip_originate',
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
                        'b.last_modified_time',
                        'b.comment',
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
}