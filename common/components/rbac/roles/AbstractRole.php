<?php
/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\rbac\roles;

use common\components\rbac\CollectorInterface;
use common\exceptions;

/**
 * Abstract role
 */
class AbstractRole
{

    protected $collector;

    protected $dbHandler;

    protected $userId;

    protected $module = '';

    protected $controller = '';

    protected $action = '';

    protected $roleId; 

    public function config(CollectorInterface $collector)
    {
        $this->collector  = $collector;
        $this->dbHandler  = $collector->getDbHandler();
        $this->userId     = $collector->getUserId();
        $this->roleId     = $collector->getRoleId();
        $this->module     = $collector->getModule() ? $collector->getModule() : '';
        $this->controller = $collector->getController() ? $collector->getController() : '';
        $this->action     = $collector->getAction() ? $collector->getAction() : '';

        return $this;
    }

    /**
     * Get valid menu
     *
     * @param $collector object
     * @return array
     */
    public function getMenus(CollectorInterface $collector)
    {
        $this->config($collector);

        $menus = [];
        $dbHandler = $this->dbHandler;

        foreach($this->collect($collector) as $res) {
            $menu = $this->getMenu($res['menu_id']);
            foreach($menu as $m) {
                if(! in_array($m, $menus)) {
                    $menus[] = $m;
                }
            }
        }

        unset($menu);
        $this->sort($menus);
        $allMenus = [];

        foreach($menus as $menu) {
            if($menu['parent'] == -1) {
                $allMenus[] = $menu;
            } else {
                $this->insertMenuNode($allMenus, $menu);
            } 
        }
        return array_reverse($allMenus);
    }

    private function getMenu($id) 
    { 
        $dbHandler = $this->dbHandler; 
        $menus = []; 
        $currentMenu = $dbHandler->getMenu($id); 
         
        do {
            if(!empty($currentMenu)) {
                $menus[] = $currentMenu;
            }
            $parent = $dbHandler->getParentMenu($currentMenu['parent']);
            $currentMenu = $parent; 
        } while($parent != null); 

        return $menus; 
    } 

    public function getAllMenus(CollectorInterface $collector)
    {
        $this->config($collector);

        $dbHandler = $this->dbHandler;
        $originMenus = $dbHandler->getAllMenus();
        $this->sort($originMenus);
        foreach($originMenus as $k) {
            if($k['parent'] == -1) {
                $menus[] = $k;
            } else {
                $this->insertMenuNode($menus, $k);
            } 
        }
        return $menus;
    }


    private function insertMenuNode(& $menus, $node)
    {
        foreach($menus as &$v) {
            if($this->isMenu($v)) {
                if($v['id'] == $node['parent']) {
                    $v['children'][] = $node;
                    break;
                }
            }
            if($this->isMenu($v) && isset($v['children'])) {
                $this->insertMenuNode($v['children'], $node);
            }
        }
    }

    private function sort(& $menus)
    {
        $temp = [];
        for($depth = -1; $depth < 10; $depth++) {
            foreach($menus as $menu) {
                if($menu['depth'] == $depth) {
                    array_push($temp, $menu);
                }
            }
        }
        $menus = $temp;
    }
 
    private function isMenu($menu)
    {
        if(is_array($menu) && isset($menu['name'], $menu['id'], $menu['parent'])) {
            return true;
        }
        return false;
    }

    public function __call($name, $params)
    {
        throw new exceptions\RuntimeException(sprintf(
            'Call undefind method %s().',
            $name
        ));
    }

    public function getId()
    {
        return $this->id ?? null;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Get all role
     *
     * @param object $collector
     * @return mixed
     */
    public function getAllRoles()
    {
        return $this->dbHandler->getAllNormalRoles();
    }

    /**
     * Get resource from role
     *
     * @param object $collector
     * @return mixed
     */
    public function getResourceFromRole($role_id)
    {
        return $this->dbHandler->getResourceFromRole($role_id);
    }

    /**
     * Add role
     *
     * @param string $name
     * @return mixed
     */
    public function addRole($name)
    {
        try {
            if($this->dbHandler->getRoleByName($name)) {
                throw new exceptions\Exception('角色已存在');
            }
        } catch(exceptions\Exception $ex) {
            throw $ex;
        }
        return $this->dbHandler->addRole($name);
    }

    /**
     * Assign role to a specific user
     *
     * @param integer $roleId
     * @param integer $userId
     * @return mix
     */
    public function assignRole($roleId, $userId)
    {
        return $this->dbHandler->setRole($roleId, $userId);
    }

    /**
     * Assign resource to role
     *
     * @param integer $roleId
     * @param array $resource_id
     * @param mixed
     */
    public function allowRule($roleId, $resource_id)
    {
        return $this->dbHandler->allowRule($roleId, $resource_id);
    }

    public function deleteRole($roleId)
    {
        return $this->dbHandler->deleteRole($roleId);
    }

    public function revokeRole($userId)
    {
        return $this->dbHandler->revokeRole($userId);
    }
}

