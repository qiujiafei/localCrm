<?php
namespace common\components\amqp;

use Yii;
use yii\base\InvalidConfigException;

class Message{

    private $_message;

    public function __construct($target){
        if(is_object($target)){
            if(!$this->validateObject($target))throw new InvalidConfigException('unavailable object');
        }elseif(is_array($target)){
            if(!$this->validateArray($target))throw new InvalidConfigException('unavailable array');
        }else{
            throw new InvalidConfigException('unavailable param');
        }
        $this->_message = $this->generateMessage($target);
    }

    public function get(){
        return $this->_message;
    }

    protected function validateObject($obj){
        if(!($obj instanceof AmqpTaskAbstract))return false;
        $attributes = get_class_vars($obj->className());
        return !isset($attributes['class']);
    }

    protected function validateArray(array $array){
        if(!isset($array['class']))return false;
        try{
            $obj = new \ReflectionClass($array['class']);
        }catch(\Exception $e){
            return false;
        }
        return $obj->isSubclassOf('\common\components\amqp\AmqpTaskAbstract');
    }

    protected function generateMessage($target){
        if(is_object($target)){
            $array['class'] = $target->className();
            $attributes = get_class_vars($array['class']);
            foreach($attributes as $name => $value){
                $array[$name] = $target->{$name};
            }
            return $array;
        }elseif(is_array($target)){
            return $target;
        }else{
            return false;
        }
    }
}
