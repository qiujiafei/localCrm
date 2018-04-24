<?php

/* * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\models\get\db;

use common\ActiveRecord\AdminUserAR;
use yii\data\ActiveDataProvider;
use Yii;
use yii\db\Query;

class SelectModel {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {
        
        return (new Query())->select([
                'a.id',
                'account',
                'a.name',
                'a.mobile',
                'a.email',
                'c.id as role_id',
                'c.name as role_name',
                'a.status'
            ])
            ->from('crm_admin_user a')
            ->leftJoin(['b'=>'crm_admin_rbac_user_roles'],'b.user_id = a.id')
            ->leftJoin(['c'=>'crm_admin_rbac_roles'],'c.id = b.rbac_roles_id')
            ->where(['a.id'=>$id])->andWhere(['<>','type',2])->one();
    }

    public static function getall($count_per_page, $page_num, $condition) {

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => AdminUserAR::find()->select([
                    'a.id',
                    'account',
                    'a.name',
                    'a.mobile',
                    'a.email',
                    "IFNULL(c.name, '') as role_name",
                    'a.status'
                ])
                ->from('crm_admin_user a')
                ->leftJoin(['b'=>'crm_admin_rbac_user_roles'],'b.user_id = a.id')
                ->leftJoin(['c'=>'crm_admin_rbac_roles'],'c.id = b.rbac_roles_id')
                ->where($condition)
                ->andWhere(['<>','type',2])
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

    /**
     * 确定是否存在
     * @param $where
     * @param null $db
     * @return bool
     */
    public function isUserExists($where,$db = null){
        return AdminUserAR::find()->where($where)->exists($db);
    }
}
