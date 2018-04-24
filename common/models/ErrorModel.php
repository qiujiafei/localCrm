<?php

namespace common\models;

use common\models\Model as CommonModel;
use Yii;

class ErrorModel extends CommonModel
{
    const ACTION_ERROR = 'error';
    public function scenarios()
    {
        return [
            self::ACTION_ERROR => [],
        ];
    }
    
    public function error()
    {
        $error = Yii::$app->getErrorHandler()->exception;
        
        $this->addError('error', $error);
        return false;
    }
    
}
