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
interface RoleInterface
{
    /**
     * Collect all resource that valid for current role
     *
     * @param $collector
     * @return \common\comoponent\rbac\Role
     */
    public function collect(CollectorInterface $collector);

    /**
     * Check is this resource is exists
     *
     * @throws exception\InvalidArgumentException
     * @param array $resource
     * @return bool
     */
    public function exists(CollectorInterface $collector, $mixed = null);

    /**
     * Get valid menu
     *
     * @param $collector object
     * @return array
     */
    public function getMenus(CollectorInterface $collector);
}
