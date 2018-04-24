<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\mutex;

/**
 * Config Interface
 */
interface ConfigInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get table name
     *
     * @return string
     */
    public function getTableName();
}
