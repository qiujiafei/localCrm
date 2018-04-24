<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\components\tokenAuthentication\exception;

use common\exceptions\Exception;

class NotEnableUserException extends Exception
{
    const ERROR = '账号已禁用';
}
