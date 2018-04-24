<?php

namespace common\components\rbac\services;

use common\components\rbac\RoleHandler;
use common\components\rbac\DbHandler;
use common\components\ActiveRecordLocator;

class RoleInvokableFactory
{
    public function __invoke($userId)
    {
        if(!is_int($userId)) {
            throw new exceptions\InvalidArgumentException(sprintf(
                'User ID should be integer, %s given, In %s.',
                gettype($userId),
                j_METHOD__
            ));
        }

        return new RoleHandler($userId, $this->getDbHandler());
    }

    private function getDbHandler()
    {
        return new DbHandler(new ActiveRecordLocator);
    }
}   

