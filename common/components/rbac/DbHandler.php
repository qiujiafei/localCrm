<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\rbac;

use common\components\ContainerInterface;
use common\exceptions;

/**
 * DB handler or a accessor
 */
class DbHandler
{
    /**
     * Main DB table of RBAC component
     *
     * @var array
     */
    private $tables = [
        'rbac_account_rules',
        'rbac_user_roles',
        'rbac_resource',
        'rbac_resource_menus',        
        'rbac_roles',
        'rbac_rules',
    ];

    /**
     * ActiveRecord object name generated by ActiveRecordLocator
     *
     * @var array
     */
    private $activeRecord;

    /**
     * @var object
     */
    private $activeRecordLocator;

    /**
     * Constructor
     *
     * @throws exceptions\InvalidArgumentException
     * @param object
     */
    public function __construct($locator)
    {
        if(! $locator instanceof ContainerInterface) {
            throw new exceptions\InvalidArgumentException(sprintf(
                "locator need instance of Psr\ContainerInterface, In %s.",
                __METHOD__
            ));
        }

        $this->activeRecordLocator = $locator;
        $this->init();
    }

    public function getAllNormalRoles()
    {
        $result = $this->activeRecord['rbac_roles']::find()
            ->where(['is_default' => 0])
            ->asArray()
            ->all();
        return $result;
    }

    public function addRole($name)
    {
        if(! is_string($name)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'Role name must be a string. In %s.',
                __METHOD__
            ));
        }
        $role = new $this->activeRecord['rbac_roles'];
        $role->name = $name;
        $role->is_default = 0;
        try {
            $result = $role->save();
        } catch(\Exception $ex) {
            return false;
        }
        return $result;
    }

    /**
     * Get resource can be access by specific role
     *
     * @param integer
     * @return array
     */
    public function getResourceFromRole($id)
    {
         $result = $this->activeRecord['rbac_rules']::find()
            ->select('{{%rbac_resource}}.*')
            ->leftJoin(['{{%rbac_resource}}'], '{{%rbac_resource}}.id = {{%rbac_rules}}.rbac_resource_id')
            ->where(['{{%rbac_rules}}.rbac_role_id' => $id])
            ->asArray()
            ->all();
        return $result;     
    }

    /**
     * Get if a resource can be acccess by a specific role
     *
     * @param integer
     * @param string $module
     * @param string $controller
     * @param string $action
     */
    public function getResourceAccessFromRole($id, $module = '', $controller = '', $action = '')
    {
        $resource = $this->activeRecord['rbac_resource']::find()
            ->where(['module_name' => $module, 'controller_name' => $controller, 'action_name' => $action])
            ->asArray()
            ->one();

        if(empty($resource)) {
            return false;
        }

        if($resource['is_default'] == 1) {
            return true;
        }

        return $this->activeRecord['rbac_rules']::find()
            ->where(['rbac_role_id' => $id, 'rbac_resource_id' => $resource['id']])
            ->exists();
    }

    /**
     * Get ActiveRecordLocator
     *
     * return ob
     * ject
     */
    public function getLocator()
    {
        return $this->activeRecordLocator;
    }

    public function getRoleByUserId($userId)
    {
        if(! is_int($userId+0)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'userId as a argument should be integer. %s given. In %s.',
                gettype($userId),
                __METHOD__
            ));
        }

        $result = $this->activeRecord['rbac_user_roles']::find()
            ->select('{{%rbac_roles}}.*')
            ->leftJoin(['{{%rbac_roles}}'], '{{%rbac_user_roles}}.rbac_roles_id = {{%rbac_roles}}.id')
            ->where(['{{%rbac_user_roles}}.user_id' => $userId])
            ->asArray()
            ->all();

        $roles = [];
        if(is_array($result) && ! empty($result)) {
            return $result;
        }
        return [];
    }

    public function hasRoleAssignUser($role_id)
    {
        return $this->activeRecord['rbac_user_roles']::find()->where(['rbac_roles_id' => $role_id])->exists();
    }

    public function getAllResource()
    {
        $result = $this->activeRecord['rbac_resource']::find()
            ->andWhere(['<>', 'is_default', '1'])
            ->asArray()
            ->all();

        return $result;
    }

    public function getResourceFromAccount($userId)
    {
        $result = $this->activeRecord['rbac_account_rules']::find()
            ->select('{{%rbac_resource}}.*')
            ->leftJoin(['{{%rbac_resource}}'], '{{%rbac_resource}}.id = {{%rbac_account_rules}}.rbac_resource_id')
            ->where(['{{%rbac_account_rules}}.rbac_user_id' => $userId])
//            ->orWhere(['=', 'is_default', '1'])
            ->asArray()
            ->all();
        return $result;

    }

    public function getResourceAccessFromAccount($userId, $module = '', $controller = '', $action = '')
    {
        $resource = $this->activeRecord['rbac_resource']::find()
            ->where(['module_name' => $module, 'controller_name' => $controller, 'action_name' => $action])
            ->asArray()
            ->one();

        if(empty($resource)) {
            return false;
        }

        if($resource['is_default'] == 1) {
            return true;
        }

        return $this->activeRecord['rbac_account_rules']::find()
            ->where(['rbac_resource_id' => $resource['id'], 'rbac_user_id' => $userId])
            ->exists();
    }

    public function getMenu($id)
    {
        $menu = $this->activeRecord['rbac_resource_menus']::find()
            ->select(['id', 'name', 'parent', 'depth'])
            ->where(['id' => $id])
            ->asArray()
            ->one();

        return $menu;     
    }

    public function getMenuUseDepth($depth)
    {
        if(!is_int($depth + 0) || $depth < 1) {
            return null;
        }

        $menus = $this->activeRecord['rbac_resource_menus']::find()
            ->select(['id', 'name', 'parent', 'depth'])
            ->where(['depth' => $depth])
            ->asArray()
            ->all();

        return $menus; 
         
    }

    public function getAllMenus()
    {
         $menus = $this->activeRecord['rbac_resource_menus']::find()
            ->select(['id', 'name', 'parent', 'depth'])
            ->asArray()
            ->all();

        return $menus; 
       
    }

    public function getParentMenu($id)
    {
        if((int)$id == -1) {
            return null;
        }

        return $this->getMenu($id);
    }

    public function init()
    {
        $activeRecord = [];
        foreach($this->tables as $v) {
            $activeRecord[$v] = $this->activeRecordLocator->getStatic($v);
        }
        $this->activeRecord = $activeRecord;
    }

    public function allowAccountRule($userId, $resource)
    {
        $data = [];

        foreach($resource as $res) {
            $data[] = [$res, $userId];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->activeRecord['rbac_account_rules']::deleteAll(['rbac_user_id' => $userId]);
            $affectedRow = \Yii::$app
                ->db
                ->createCommand()
                ->batchInsert('{{%rbac_account_rules}}', ['rbac_resource_id', 'rbac_user_id'], $data)
                ->execute();
            $transaction->commit();
        } catch(\Exception $ex) { 
            $transaction->rollback();
            throw $ex;
        }

        return $affectedRow;
    }

    public function deleteRole($roleId)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->activeRecord['rbac_roles']::deleteAll(['id' => $roleId]);
            $this->activeRecord['rbac_rules']::deleteAll(['rbac_role_id' => $roleId]); 
            $transaction->commit();
        } catch(\Exception $ex) { 
            $transaction->rollback();
            throw $ex;
        }       
    }

    public function revokeRole($userId)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $this->activeRecord['rbac_user_roles']::deleteAll(['user_id' => $userId]);
            $transaction->commit();
        } catch(\Exception $ex) { 
            $transaction->rollback();
            throw $ex;
        }       
    }

    public function fobidenAccountRule($userId, $resource)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $affectedRow = $this->activeRecord['rbac_account_rules']::deleteAll(['rbac_user_id' => $userId, 'rbac_resource_id' => $resource]);
            $transaction->commit();
        } catch(\Exception $ex) { 
            $transaction->rollback();
            throw $ex;
        }
        return $affectedRow;
    }

    public function allowRule($roleId, $resource)
    {
        $data = [];
        
        $affectedRow = $this->activeRecord['rbac_rules']::deleteAll(['rbac_role_id' => $roleId]);

        if(empty($resource)) {  
            return $affectedRow;
        }

        foreach($resource as $res) {
            $data[] = [$res, $roleId];
        }


        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $affectedRow = \Yii::$app
                ->db
                ->createCommand()
                ->batchInsert('{{%rbac_rules}}', ['rbac_resource_id', 'rbac_role_id'], $data)
                ->execute();
                //->execute();
            $transaction->commit();
        } catch(\Exception $ex) { 
            $transaction->rollback();
            throw $ex;
        }

        return $affectedRow;
    }

    public function getRoleByName($roleName)
    {
        $role = $this->activeRecord['rbac_roles']::find()
            ->where(['name' => $roleName])
            ->asArray()
            ->one();

        return $role; 

    }

    public function getRoleById($roleId)
    {
        $role = $this->activeRecord['rbac_roles']::find()
            ->where(['id' => $roleId])
            ->asArray()
            ->one();

        return $role; 

    }

    public function setRole($roleId, int $userId)
    {
        if(is_numeric($roleId)) {
            if(! $role = $this->getRoleById($roleId)) {
                throw new exceptions\RuntimeException(sprintf(
                    'Role ID a.k.a %s is not found. In %s.',
                    $roleId,
                    __METHOD__
                ), 1);
            }
        } else {
            if(! $role = $this->getRoleByName($roleId)) {
                throw new exceptions\RuntimeException(sprintf(
                    'Role Name a.k.a %s is not found. In %s.',
                    $roleId,
                    __METHOD__
                ), 1);
            }
        }
        $roleId = $role['id'];

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $userRoles = $this->activeRecord['rbac_user_roles']::find()
                ->where(['user_id' => $userId])
                ->all();
            foreach($userRoles as $userRole) {
                $userRole->delete();
            }

            $role = new $this->activeRecord['rbac_user_roles'];
            $role->user_id = $userId;
            $role->rbac_roles_id = $roleId;
            $role->save();
            $transaction->commit();
        } catch(\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }

        return $role->id; 
    }
}
