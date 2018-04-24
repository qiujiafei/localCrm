<?php

/**
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\models\get;

use common\models\Model as CommonModel;
use admin\modules\adminuser\models\get\db\SelectModel;
use admin\modules\authorization\models\ResourceModel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';

    public $rbac;
    public $count_per_page;
    public $page_num;
    public $id;
    public $account;
    public $name;
    public $password;
    public $email;
    public $mobile;
    public $keyword;
    public $status;
    public $role_id;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id', 'name', 'rbac'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'keyword', 'name', 'status', 'role_id'],
        ];
    }

    public function rules() {
        return [
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
            ['status', 'in', 'range' => [0, 1], 'message' => 2004],
            [['status', 'store_id'], 'integer', 'message' => 2004],
        ];
    }

    public function actionGetone() {
        
        try {
            $result = SelectModel::getone($this->id);
            if (!$result) {
                throw new \Exception('无法获取-1', 2005);
            }
            $user_id = $result['user_id']??'0';
            $role_id = $result['role_id']??'0';
            //获取用户当前角色的权限列表
            $ResourceModel = new ResourceModel([
                'scenario' => ResourceModel::ASSIGN_ROLE,
                'attributes' => array_merge(
                    ['role_id'=>$role_id,'user_id'=>$user_id],
                    ['rbac' => $this->rbac]
                ),
            ]);
            $get_resource = $ResourceModel->getAll();
            $result['resource_list'] = $get_resource;
        } catch (\Exception $e) {
            $this->addError('getone', 2005);
            return false;
        }

        return $result;
    }

    public function actionGetall() {
        
        try {
            $conditions = ['and'];
            if (!empty(trim($this->keyword))){
                array_push($conditions, ['like', 'a.name', trim($this->keyword)]);
            }
            if (!empty($this->role_id)){
                array_push($conditions, ['c.id'=>$this->role_id]);
            }
            $result = SelectModel::getall($this->count_per_page, $this->page_num,$conditions);
        } catch (\Exception $e) {
            $this->addError('getall', 2005);
            return false;
        }
        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'list' => $result->models,
        ];
    }

}
