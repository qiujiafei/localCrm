<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace common\components\mutex\services;

use common\components\mutex\Mutex;
use common\components\mutex\ConfigWithParam;

class MutexFactory
{
    public static function getMutex($timeout = null)
    {
        $config = new ConfigWithParam();
        return new Mutex($config, $timeout);
    }
}

