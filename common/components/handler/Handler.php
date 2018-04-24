<?php
namespace common\components\handler;

use yii\base\Component;
use common\traits\ErrCallbackTrait;

class Handler extends Component{

    use ErrCallbackTrait;

    /**
     * 获取对象或数组的多个属性
     *
     * @param Object|Array $obj 需要处理的对象
     * @param array $attributes 需要获取的属性
     * 如果$attributes的键名是整数，则返回以属性名为键名，属性值为键值的数组
     * 如果$attributes的键名是字符串，则返回以字符串为键名，属性值为键值的数组
     * 各属性可分别配置
     * 特殊键名：_func，配置：
     * _func => [
     *     'attributeName' => callable $function,
     * ],
     * 配置该键名可指定特定属性调用指定函数并返回函数结果
     *
     * $function = function(`attributeResult`, `callbackName`){}
     * `attributeResult`: $obj对象属性值
     * `callbackName`: 回调时的键名
     * 
     * @return array
     */
    public static function getMultiAttributes($obj, array $attributes){
        $objAttributes = [];
        $attrFunc = $attributes['_func'] ?? [];
        unset($attributes['_func']);
        foreach($attributes as $key => $attribute){
            if(is_object($obj)){
                $attrResult = $obj->$attribute;
            }elseif(is_array($obj)){
                $attrResult = $obj[$attribute] ?? null;
            }else{
                return false;
            }
            $objAttributes[is_int($key) ? $attribute : $key] = (isset($attrFunc[$attribute]) && is_callable($attrFunc[$attribute])) ? call_user_func($attrFunc[$attribute], $attrResult, is_int($key) ? $attribute : $key) : $attrResult;
        }
        return $objAttributes;
    }

    public static function implodeUrlParams(array $params, bool $urlencode = true){
        $paramsImploded = [];
        foreach($params as $paramTitle => $paramValue){
            $paramsImploded[] = $paramTitle . '=' . ($urlencode ? urlencode($paramValue) : $paramValue);
        }
        return implode('&', $paramsImploded);
    }

    public static function generateBKDRHash(string $string, $hashMethod = false){
        if($hashMethod){
            try{
                $string = hash($hashMethod, $string);
            }catch(\Exception $e){
                return false;
            }
        }
        if(empty($string))return false;
        $seed = 131;
        $hash = 0;
        $count = strlen($string);
        for($i = 0; $i < $count; $i++){
            $hash = ((floatval($hash * $seed) & 0x7FFFFFFF) + ord($string[$i])) & 0x7FFFFFFF;
        }
        return ($hash & 0x7FFFFFFF);
    }
}
