<?php

namespace common\components\rbac;

use common\components\rbac\DbHandler;
use common\components\exceptions;

class RoleHandler
{
    private $userId;

    private $dnHandler;

    public function __construct(int $userId, $dbHandler)
    {
        if(! ($dbHandler instanceof DbHandler)) {
            throw new exceptions\RuntimeException(sprintf(
                'DbHandler is not a vaild class. In %s.',
                __METHOD__
            ));
        }
        $this->dbHandler = $dbHandler;
        $this->userId = $userId;
    }

    public function changeRole(string $roleName, $userId=null)
    {
        if($userId === null) {
            $userId = $this->userId;
        }
        return $this->dbHandler->setRole($roleName, $userId);
    }
}
