<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace admin\components\tokenAuthentication\exception;

use yii\base\Exception;

class InvalidArgumentException extends Exception
{
    public function getName()
    {
        return "param not found";
    }
}
