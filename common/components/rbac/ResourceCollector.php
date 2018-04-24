<?php
/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\rbac;

use common\exceptions;
use common\components\rbac\exceptions as rbacException;
use common\components\rbac\roles\RoleInterface;
use yii\web\Controller as YiiController;
use common\components\tokenAuthentication\AccessTokenAuthentication;

/**
 * Resource manager, act as a resource collect to get specific resource.
 */
class ResourceCollector implements CollectorInterface
{
    /**
     * @var string
     */
    const DEFAULT_ROLE_NAME = 'none_role_user';

    /**
     * Role object namespace
     *
     * @var string
     */
    private $roleNamespace = 'common\\components\\rbac\\roles';

    /**
     * DB handler
     *
     * @var object
     */
    private $dbHandler;

    /**
     * Encapsulate of ROLEs
     *
     * @var object
     */
    private $role;

    /**
     * Employee user ID
     *
     * @var integer
     */
    private $userId;

    /**
     * Module name from Yii2
     *
     * @var string
     */
    private $module;

    /**
     * Controller name from Yii2
     *
     * @var string
     */
    private $controller;

    /**
     * Action name from Yii2
     *
     * @var string
     */
    private $action;

    /**
     * Resource aggregate container
     *
     * @var object
     */
    private $aggregate;

    /**
     * Assign role a.k.a Normal to undefind role
     *
     * @var bool
     */
    private $enableAutoAssignRole = true;

    private $roleId;

    /**
     * Constructor
     *
     * @throws exceptions\RuntimeException
     * @throws exceptions\InvalidArgumentException
     * @param object $dbHandler
     * @param mixed $require
     * @param object $aggregate
     */
    public function __construct($dbHandler, $require, $aggregate)
    {
        if(! $dbHandler instanceof DbHandler) {
            throw new exceptions\RuntimeException(sprintf(
                'dbHandler is not a valid instance. In %s.',
                __METHOD__
            ));
        }

        if(! is_array($require) && ! ($require instanceof YiiController)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'require as a argument should be array or YiiController, if is array, it should content keys like %s. %s given. In %s.',
                '1. module 2. controller 3. action 4. userId',
                gettype($require),
                __METHOD__
            ));
            foreach(['module', 'controller', 'action', 'userId'] as $key) {
                if(! array_key_exists($key, $require)) {
                    throw new exceptions\InvalidArgumentException(
                        'require need keys in 1. module 2. controller 3. action 4. userId.'
                    );
                }
            }
        }

        $this->dbHandler = $dbHandler;
        $this->config($require);
        $this->setAggregate($aggregate);
    }

    /**
     * Get all resource can be accessed.
     *
     * @throws exceotions\RuntimeException
     * @return object
     */
    public function collect()
    {
        if(empty($this->role)) {
            throw new exceptions\RuntimeException(sprintf(
                'Role is not set yet. In %s.',
                __METHOD__
            ));
        }

        $resource = [];
        foreach($this->role as $role) {
            if($role instanceof RoleInterface) {
                $accessResource = $role->collect($this);
                if(! in_array(current($accessResource), $resource))
                    $resource = array_merge($resource, $accessResource);
            }
        }

        $this->aggregate->push($resource);

        return $this->aggregate;
    }

    public function getAllMenus()
    {
        return current($this->role)->getAllMenus($this);
    }

    /**
     * Check is this resource is exists
     *
     * @return bool
     */
    public function exists($resource = null)
    {
        if(is_array($resource) && isset($resource['userId'])) {
            $roles = $this->getRoleNameByUserId($resource['userId']);
        } elseif (is_array($resource) && isset($resource['roleId'])) {
            $roles = $this->getRoleObjectByRoleId($resource['roleId']);
        } else {
            $roles = $this->role;
        }

        $this->roleId = current($roles)->getId();

        foreach($roles as $role) {
            if($role instanceof RoleInterface) {
                if($role->exists($this, $resource)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getRoleId()
    {
        return $this->roleId;
    }

    private function getRoleObjectByRoleId($roleId)
    {
        $role = $this->dbHandler->getRoleById($roleId);
        return [$this->resolveRoleObject($role)]; 
    }

    public function getMenus()
    {

        $allRoleMenus = [];
        foreach($this->role as $role) {
            if($role instanceof RoleInterface) {
                if($menus = $role->getMenus($this)) {
                    $allRoleMenus = array_merge_recursive($allRoleMenus, $menus);
                }
            }
        }

        return $this->sort($allRoleMenus);
       
    }

    /**
     * sort menu to ragular
     *
     * @param array $menus
     * @return array
     */
    private function sort(array $menus)
    {
        $sorted = [];

        foreach($menus as $k => $v) {
            $sorted[$v['id']] = $v;
            if(isset($v['children'])) {
                $sorted[$v['id']]['children'] = $this->sort($v['children']);
            }
        }

        ksort($sorted);

        return array_values($sorted);
    }

    /**
     * Set into resource aggregate
     *
     * @throws exceptions\RuntimeException
     * @param object
     * @return object
     */
    private function setAggregate($aggregate)
    {
        if(! $aggregate instanceof AggregateInterface) {
            throw new exceptions\RuntimeException(
                'Can not get valid resource aggregate, In %s.',
                __METHOD__
            );
        }

        $this->aggregate = $aggregate;

        return $this;
    }

    /**
     * Config this collector
     *
     * @throws exceptions\RuntimeException
     * @param mixed
     * @return object
     */
    private function config($require)
    {
        if(is_array($require)) {
            $role = $this->getRoleNameByUserId($require['userId']);
            $this->userId = $require['userId'];
            $this->module = $require['module'];
            $this->controller = $require['controller'];
            $this->action = $require['action'];
        } elseif($require instanceof YiiController) {
            try {
                $id = AccessTokenAuthentication::getUser()['id'];
            } catch(\Throwable $ex) {
                throw new exceptions\RuntimeException('can not found login user.');
            }

            $role = $this->getRoleNameByUserId($id);
            $this->userId = $id;
            $this->module = $require->module->id;
            $this->controller = $require->id;
            $this->action = $require->action->id;
        }

        if(empty($role)) {
            throw new rbacException\RoleNotSetException('Role not set for this specific user.');
        }

        $this->role = $role;

        return $this;
    }

    /**
     * Get role name by user id
     *
     * @param integer
     * @return array
     */
    public function getRoleNameByUserId($userId)
    {
        $dbHandler = $this->getDbHandler();
        $roleNames = $dbHandler->getRoleByUserId($userId);

        if(empty($roleNames) && $this->enableAutoAssignRole) {
            $roleNames[] = ['name' => self::DEFAULT_ROLE_NAME];
        }

        return $this->buildRoles($roleNames);
    }

    /**
     * Getter for user id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Getter for module name
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Getter for controller name
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Getter for action name
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Build role instances
     *
     * @param array
     * @return array
     */
    private function buildRoles(array $roles)
    {
        $roleObjects = [];
        foreach($roles as $role) {
            $role = $this->getRoleObjectName($role);
            $roleObjects[] = $this->resolveRoleObject($role);
        }
        
        return array_map(function ($element) { if(! empty($element)) return $element; }, $roleObjects);
    }

    /**
     * Get object name
     *
     * @param array
     * @return array
     */
    private function getRoleObjectName(array $name)
    {
        $objectName = '';
        $splice = explode('_', $name['name']);
        foreach($splice as $v) {
            $objectName .= ucfirst($v);
        }

        $name['name'] = $objectName;

        return $name;
    }

    /**
     * Resolve role object
     *
     * @throws exceptions\RuntimeException
     * @param string
     * @return object
     */
    private function resolveRoleObject($name)
    {
        $namespace = 'common\\components\\rbac\\roles';

        $className = $namespace . '\\' . $name['name'];

        if(class_exists($className)) {
            $role = new $className; 
        } else {
            $className = $namespace . '\\' . 'Normal';
            $role = new $className($name['id'], $name['name']);
        }

        if(! $role instanceof RoleInterface) {
            throw new exceptions\RuntimeException(sprintf(
                'Role object should be instance of RoleInterface. %s is not. In %s.',
                get_class($role),
                __METHOD__
            ));
        } else {
            return $role;
        }

        throw new exceptions\RuntimeException(sprintf(
            'Can not found Role object called %s. In %s.',
            $name['name'],
            __METHOD__
        ));
    }

    /**
     * update resource to status fobiden
     *
     * @param array $resource
     * @return bool 
     */
    public function fobiden($userId, array $resource)
    {
        $role = $this->getRoleNameByUserId($userId);
        foreach($role as $v) {
            if($v instanceof RoleInterface) {
                $row = $v->fobiden($this, $userId, $resource);
                return $row;
            }
        }

        return 0;
    }

    /**
     * update resource to status allow
     *
     * @param array $resource
     * @return bool 
     */
    public function allow($userId, array $resource)
    {
        $role = $this->getRoleNameByUserId($userId);
        foreach($role as $v) {
            if($v instanceof RoleInterface) {
                $row = $v->allow($this, $userId, $resource);
                return $row;
            }
        }

        return 0; 
    }

    /**
     * Get current role.
     *
     * @return object
     */
    public function getCurrentRole()
    {
        if(empty($this->role)) {
            throw new exceptions\RuntimeException(sprintf(
                'Role is not set yet. In %s.',
                __METHOD__
            ));
        }
        return current($this->role);
    }

    /**
     * Getter for db handler
     *
     * @return object
     */
    public function getDbHandler()
    {
        return $this->dbHandler;
    }
}
