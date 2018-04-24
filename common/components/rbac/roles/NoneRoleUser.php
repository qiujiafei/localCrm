<?php
/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\rbac\roles;

use common\components\rbac\CollectorInterface;

/**
 * Role encapsulate for RBAC component
 */

class NoneRoleUser extends AbstractRole implements RoleInterface
{
    /**
     * Collect all resource that valid for current role
     *
     * @param $collector
     * @return \common\comoponent\rbac\Role
     */
    public function collect(CollectorInterface $collector)
    {
        $this->config($collector);
        return $this->dbHandler->getResourceFromAccount($this->userId);
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

        if($mixed == null) {
            return $this->dbHandler->getResourceAccessFromAccount($this->userId, $this->module, $this->controller, $this->action);
        } else {
            if(! is_array($mixed) || ! isset($mixed['userId'], $mixed['module'], $mixed['controller'], $mixed['action'])) {
                return false;
            }
            return $this->dbHandler->getResourceAccessFromAccount($mixed['userId'], $mixed['module'], $mixed['controller'], $mixed['action']);
        }
    }

    /**
     * update resource to status fobiden
     *
     * @param array $resource
     * @return bool 
     */
    public function fobiden(CollectorInterface $collector, $userId, $resource)
    {
        if(empty($resource)) {
            return true;
        }
        $this->config($collector);
        return $this->dbHandler->fobidenAccountRule($userId, $resource);

    }

    /**
     * update resource to status allow
     *
     * @param array $resource
     * @return bool 
     */
    public function allow(CollectorInterface $collector, $userId, $resource)
    {
        if(empty($resource)) {
            return true;
        }
        $this->config($collector);
        return $this->dbHandler->allowAccountRule($userId, $resource);
    }
} 
