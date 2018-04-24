<?php

namespace common\components\rbac;

interface CollectorInterface
{
    public function collect();

    /**
     * Check is this resource is exists
     *
     * @throws exception\InvalidArgumentException
     * @return bool
     */
    public function exists();

    /**
     * update resource to status fobiden
     *
     * @param array $resource
     * @return bool 
     */
    public function fobiden($userId, array $resource);


    /**
     * update resource to status allow
     *
     * @param array $resource
     * @return bool 
     */
    public function allow($userId, array $resource);

}
