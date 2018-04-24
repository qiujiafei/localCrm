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
use common\components\rbac\roles\Admin;
use Yii;

class RoleModel extends CommonModel
{
    const GET_ALL = 'get_all';
    const ADD = 'add';
    const DELETE = 'delete';
    const REVOKE = 'revoke';
    const GET_CURRENT_ROLE = 'get_current_role';

    public $rbac;
    public $name;
    public $role_id;
    public $user_id;

    public function rules()
    {
        return [
            [
                ['name', 'role_id', 'user_id'],
                'required',
                'message' => 2004
            ],
            [
                ['name'],
                'string',
                'max' => 10,
                'tooLong' => 20010, 
            ],
            [
                ['role_id', 'user_id'],
                'integer',
                'message' => 2004,
            ],
        ];
    }

    public function scenarios()
    {
        return [
            self::GET_ALL => ['rbac'],
            self::ADD => ['rbac', 'name'],
            self::DELETE => ['rbac', 'role_id'],
            self::REVOKE => ['rbac', 'user_id'],
            self::GET_CURRENT_ROLE => ['rbac'],
        ];
    }
    
    public function getAll()
    {
        $normalRole = $this->rbac->getCurrentRole();
        
        try {
            $roles = $normalRole->getAllRoles();
        } catch(exceptions\Exception $ex) {
            throw $ex;
        }

        return $roles;
    }

    public function getCurrentRole()
    {
        $normalRole = $this->rbac->getCurrentRole();
        return [
           'id' => $normalRole->getId(),
           'name' => $normalRole->getName(),

        ];
    }

    public function delete()
    {
        if($this->rbac->getDbhandler()->hasRoleAssignUser($this->role_id)) {
            $this->addError('delete', 20011);
            return false; 
        }

        $this->rbac->getCurrentRole()->deleteRole($this->role_id);
        return [];
    }

    public function revoke()
    {
        $this->rbac->getCurrentRole()->revokeRole($this->user_id);
        return [];
    }

    public function add()
    {
        $normalRole = $this->rbac->getCurrentRole();
 
        try {
            $roles = $normalRole->addRole($this->name);
        } catch(exceptions\Exception $ex) {
            throw $ex;
        }

        return $roles;
    }
}
