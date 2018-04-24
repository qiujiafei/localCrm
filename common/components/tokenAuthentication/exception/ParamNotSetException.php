<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace common\components\tokenAuthentication\exception;

use yii\base\Exception;

class ParamNotFoundException extends Exception
{
    public function getName()
    {
        return "param not found";
    }
}

