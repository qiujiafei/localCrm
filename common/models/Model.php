<?php
namespace common\models;

use yii\base\InvalidCallException;

class Model extends \yii\base\Model{
    
    /**
     * 获取错误码
     *
     * @return int
     */
    public function getErrorCode(){
        if($this->hasErrors()){
            return $this->getStrValue($this->getErrors());
        }else{
            return 0;
        }
    }

    /**
     * 递归获取数组当前键值
     *
     * @param $arr array 数组
     *
     * @return str
     */
    private function getStrValue(array $arr){
        $currentValue = current($arr);
        if(is_array($currentValue)){
            return $this->getStrValue($currentValue);
        }else{
            return $currentValue;
        }
    }

    /**
     * 通用处理流程
     *
     * @return boolean|array
     */
    public function process(){
        $function = $this->getFunctionName();
        if(method_exists($this, $function)){
            if(!$this->validate())return false;
            return call_user_func([$this, $function]);
        }else{
            throw new InvalidCallException;
        }
    }

    /**
     * 获取方法名称
     *
     * @return string
     */
    private function getFunctionName(){
        return preg_replace_callback('/_[a-z]{1}/', function($matches){
            $match = current($matches);
            return strtoupper($match[1]);
        }, $this->scenario);
    }
}
