<?php
namespace common\models;

use Yii;
/**
 * 复写验证器基类
 *
 * 错误返回值由array转换成integer，对应错误码
 *
 * 增加默认错误码，当返回未定义错误码时转换成默认错误码
 *
 * 验证通过由empty($result)转换成($result === true)
 */
class Validator extends \yii\validators\Validator{

    public $defaultErrorCode = 9003;

    public function validateAttribute($model, $attribute){
        $result = $this->validateValue($model->$attribute);
        if($result !== true){
            $this->addError($model, $attribute, $result);
        }
    }

    public function validate($value, &$error = null){
        $result = $this->validateValue($value);
        if($result === true){
            return true;
        }
        $params['attribute'] = Yii::t('yii', 'the input value');
        if (is_array($value)) {
            $params['value'] = 'array()';
        } elseif (is_object($value)) {
            $params['value'] = 'object';
        } else {
            $params['value'] = $value;
        }
        $error = Yii::$app->getI18n()->format($result, $params, Yii::$app->language);

        return false;
    }

    public function addError($model, $attribute, $message, $params = []){
        $message = is_int($message) ? $message : $this->defaultErrorCode;
        parent::addError($model, $attribute, $message, $params);
    }
}
