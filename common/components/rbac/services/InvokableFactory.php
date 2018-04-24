<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\rbac\services;

use common\components\rbac\DbHandler;
use common\components\rbac\ResourceCollector;
use common\components\rbac\ResourceAggregate;
use common\components\rbac\AggregateInterface;
use common\components\ActiveRecordLocator;
use common\exceptions;

class InvokableFactory
{
    public function __invoke($userId)
    {
        $dbHandler = $this->getDbHandler();
        $resourceCollector = $this->configCollector($dbHandler, $userId, $this->getResourceAggregate());

        return $resourceCollector;
    }

    private function getResourceAggregate()
    {
        return new ResourceAggregate();
    }

    private function configCollector($dbHandler, $userId, $aggregate)
    {
        if(! $dbHandler instanceof DbHandler) {
            throw new exceptions\RuntimeException(sprintf(
                'DbHandler can not be used in %s. It should be instance of %s. %s is not.',
                __METHOD__,
                DbHandler::class,
                get_class($dbHandler)
            ));
        }

        if(! $aggregate instanceof AggregateInterface) {
            throw new exceptions\RuntimeException(sprintf(
                'Can not get valid resource aggregate, In %s.',
                __METHOD__
            ));
        }

        return new ResourceCollector($dbHandler, $userId, $aggregate);
    }

    private function getDbHandler()
    {
        return new DbHandler(new ActiveRecordLocator);
    }
}
