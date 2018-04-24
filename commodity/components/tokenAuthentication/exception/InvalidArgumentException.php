<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\components\tokenAuthentication\exception;

use common\exceptions\Exception;

class InvalidArgumentException extends Exception
{
    public function getName()
    {
        return "param not found";
    }
}
