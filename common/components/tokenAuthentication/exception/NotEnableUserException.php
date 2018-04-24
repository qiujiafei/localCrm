<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\tokenAuthentication\exception;

use yii\base\Exception;

class NotEnableUserException extends Exception
{
    public function getMessage()
    {
        return '账号已禁用';
    }
}
