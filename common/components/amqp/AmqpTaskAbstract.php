<?php
namespace common\components\amqp;

use Yii;
use yii\base\Object;

abstract class AmqpTaskAbstract extends Object{

    abstract public function run();

}
