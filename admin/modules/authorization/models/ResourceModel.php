<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace admin\modules\Authorization\models;

use common\models\Model as CommonModel;
use common\components\tokenAuthentication\AccessTokenAuthentication as User;
use common\exceptions;
use Yii;

class ResourceModel extends CommonModel
{
    const GET_ALL = 'get_all';
    const GET_ALLOW = 'get_allow';
    const ASSIGN_ROLE = 'assign_role';
    const ASSIGN_RESOURCE = 'assign_resource';

    public $rbac;
    public $resource;
    public $user_id;
    public $role_id;
    public $allow;

    public function rules()
    {
        return [
            [
                ['user_id', 'role_id', 'resource', 'rbac'],
                'required',
                'message' => 2004
            ],
            [
                ['user_id', 'role_id'],
                'integer',
                'message' => 3006
            ],
        ];
    }
    
    public function scenarios()
    {
        return [
            self::GET_ALL           => ['rbac', 'role_id'],
            self::GET_ALLOW         => ['rbac', 'role_id'],
            self::ASSIGN_ROLE       => ['user_id', 'role_id', 'rbac'],
            self::ASSIGN_RESOURCE   => ['role_id', 'allow', 'rbac'],
        ];
    }
    
    public function getAll()
    {
        $dbHandler = $this->rbac->getDbHandler();
        $resource = $dbHandler->getAllResource();

        $allResource = [];

        foreach($resource as $res) {
            $menu = $dbHandler->getMenu($res['menu_id']);
            $menu = (isset($menu['name']) ? $menu['name'] : '其他');
            $allResource[$menu][] = [
                'id' => $res['id'],
                'name' => $res['name'],
                'is_valid' => $this->rbac->exists([
                    'roleId'        => $this->role_id,
                    'module'        => $res['module_name'],
                    'controller'    => $res['controller_name'],
                    'action'        => $res['action_name'],
                ]),
            ];
        }

        return $allResource; 
    }

    public function getAllow()
    {
        return $this->rbac->getCurrentRole()->getResourceFromRole($this->role_id);
    }

    public function assignRole()
    {
        if($this->rbac->getCurrentRole()->assignRole($this->role_id, $this->user_id)) {
            return [];
        }
        return false;
    }

    public function assignResource()
    {
        $allow = 0;
        $allow = $this->rbac->getCurrentRole()->allowRule($this->role_id, $this->allow);
        return [
            'updated' => $allow,
        ];
    } 
}
