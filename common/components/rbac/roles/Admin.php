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
class Admin extends AbstractRole implements RoleInterface
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
        $resource = $this->getAllResource($this->userId);

        return $resource;

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
        return true;
    }

    private function getAllResource($userId)
    {
        return $this->dbHandler->getAllResource();
    }

    /**
     * update resource to status fobiden
     *
     * @param array $resource
     * @return bool 
     */
    public function fobiden(CollectorInterface $collector, $userId, $resource)
    {
        return 0;
    }

    /**
     * update resource to status allow
     *
     * @param array $resource
     * @return bool 
     */
    public function allow(CollectorInterface $collector, $userId, $resource)
    {
        return 0;
    }
}
