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
 * Role encapsulate for RBAC component
 */

class Normal extends AbstractRole implements RoleInterface
{
    protected $id;

    protected $name;

    public function __construct($id, $name)
    {
        $this->name = $name;
        $this->id = $id;
    }

    /**
     * Collect all resource that valid for current role
     *
     * @param $collector
     * @return \common\comoponent\rbac\Role
     */
    public function collect(CollectorInterface $collector)
    {
        $this->config($collector);
        return $this->dbHandler->getResourceFromRole($this->id);
    }

    /**
     * Check is this resource is exists
     *
     * @throws exception\InvalidArgumentException
     * @param array $resource
     * @return bool
     */
    public function exists(CollectorInterface $collector, $mixed = null)
    {
        $this->config($collector);

        if($mixed  === null) {
            return $this->dbHandler->getResourceAccessFromRole($this->roleId, $this->module, $this->controller, $this->action);
        } else {
            if(! is_array($mixed) || ! isset($mixed['roleId'], $mixed['module'], $mixed['controller'], $mixed['action'])) {
                return false;
            }
            return $this->dbHandler->getResourceAccessFromRole($mixed['roleId'], $mixed['module'], $mixed['controller'], $mixed['action']);
        }
    }
}
