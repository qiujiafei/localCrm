<?php
namespace common\components;

use Yii;
use yii\base\Object;
use common\traits\ErrCallbackTrait;

class ErrCallback extends Object{

    use ErrCallbackTrait;

    public function callback($return = 'throw', $exceptionMsg = '', $exception = '\Exception'){
        return self::errCallback($return, $exceptionMsg, $exception);
    }
}
