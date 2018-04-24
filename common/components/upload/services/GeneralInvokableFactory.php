<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace common\components\upload\services;

use common\exceptions;
use common\components\upload\Config;
use common\components\upload\FileManager;

class GeneralInvokableFactory
{
    public function __invoke($path = '')
    {
        return new FileManager($path);
    }
}
