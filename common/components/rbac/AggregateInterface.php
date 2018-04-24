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
interface AggregateInterface
{
    /**
     * Convert aggregate to array
     *
     * @return array
     */
    public function toArray();

    /**
     * Push into resource
     *
     * @param array $resource
     * @return object
     */
    public function push(array $resource);

}
