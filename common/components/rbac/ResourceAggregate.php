<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\rbac;

/**
 * Valid Resource Aggregate
 */
class ResourceAggregate implements AggregateInterface
{

    /**
     * @var array
     */
    private $_resource;

    /**
     * Convert aggregate to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_resource;
    }

    /**
     * Push into resource
     *
     * @param array $resource
     * @return object
     */
    public function push(array $resource)
    {
        $this->_resource = $resource;
        return $this;
    }
}
