<?php
namespace common\models;

use Yii;

abstract class ObjectAbstract extends \yii\base\Object{

    protected $AR;

    /**
     * 与数据表字段对应的模型属性
     *
     * @return array
     */
    abstract protected function _gettingList() : array;

    abstract protected function _settingList() : array;

    public function __get($name){
        try{
            return parent::__get($name);
        }catch(\Exception $e){
            if(is_null($this->AR)){
                throw $e;
            }else{
                return $this->_getModelAttribute($name);
            }
        }
    }

    public function __set($name, $value){
        try{
            return parent::__set($name, $value);
        }catch(\Exception $e){
            if(is_null($this->AR)){
                throw $e;
            }else{
                return $this->_setModelAttribute($name, $value);
            }
        }
    }

    public function __call($name, $params){
        if($params && strpos($name, 'set') === 0 && !is_null($this->AR)){
            $attributeName = lcfirst(substr($name, 3));
            return $this->_setModelAttribute($attributeName, $params[0], $params[1] ?? 'throw');
        }elseif(strpos($name, 'get') === 0 && !is_null($this->AR)){
            $attributeName = lcfirst(substr($name ,3));
            return $this->_getModelAttribute($attributeName, $params[0] ?? 'throw');
        }else{
            return parent::__call($name, $params);
        }
    }

    private function _getModelAttribute($name, $return = 'throw'){
        if(in_array($name, $this->_gettingList())){
            $fieldName = $this->_generateActiveRecordFieldName($name);
            return $this->AR->$fieldName;
        }else{
            return Yii::$app->EC->callback($return, 'getting unknown field: ' . $name);
        }
    }

    private function _setModelAttribute($name, $value, $return = 'throw'){
        if(in_array($name, $this->_settingList())){
            $fieldName = $this->_generateActiveRecordFieldName($name);
            return Yii::$app->RQ->AR($this->AR)->update([
                $fieldName => $value,
            ], $return);
        }else{
            return Yii::$app->EC->callback($return, 'setting unknown field: ' . $name);
        }
    }

    private function _generateActiveRecordFieldName($attributeName){
        return preg_replace_callback('/[A-Z]{1}/', function($matches){
            return ('_' . strtolower($matches[0]));
        }, $attributeName);
    }
}
