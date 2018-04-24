<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/11
 * Time: 14:06
 */
namespace commodity\models;
use common\models\Model;
use yii\db\ActiveRecord;

class AttributeLogicModel
{
    /**
     * @param Model $model
     * @param bool $token false表示该值被清除，若需要，该值传递true
     * @return array
     * 根据场景获取需要的值，目前不支持自定义过滤
     */
    public static function getAllowAttributes(Model $model,$token = false)
    {
        //所有属性
        $attributes = $model->getAttributes();
        //场景
        $scenario = $model->getScenario();
        //场景字段
        $scenarioFields = $model->scenarios();

        $data = [];
        if (isset($scenarioFields[$scenario])){
            $fields = $scenarioFields[$scenario];
            foreach ($fields as $name){
                $data[$name] = $attributes[$name];
            }
            if (isset($data['token']) && false === $token){
                unset($data['token']);
            }
        }

        return $data;
    }

    /**
     * 替换model中的值，返回其本身
     * @param ActiveRecord $model
     * @param array $attr
     * @return ActiveRecord
     */
    public static function setReplaceAttributes(ActiveRecord $model,$attr=[])
    {
        //允许填充的字段
        $trueAttributes = $model->attributes();
        if (is_array($attr)) {
            foreach($attr as $field=>$value) {
                if (in_array($field,$trueAttributes)) {
                    $model->setAttribute($field,$value);
                }
            }
        }
        return $model;
    }

    /**
     * 获取规定字段的值
     * @param array $data
     * @param array $fields
     * @return array
     */
    public static function getFieldsOfData($data=[],$fields=[])
    {
        if (empty($data) || ! is_array($data)) {
            return [];
        }
        $return = [];
        if (is_array($fields)) {
            foreach($fields as $field) {
                if (isset($data[$field])) {
                    $return[$field] = $data[$field];
                }
            }
            return $return;
        }
        if (is_object($data) || is_resource($data)) {
            return [];
        }
        foreach ($data as $field=>$value) {
            if (isset($data[$fields])) {
                $return[$field] = $value;
            }
        }
        return $return;
    }

    /**
     * 获取只被允许的值
     * @param $data
     * @param array $allowFields
     * @return array
     */
    public static function filterAllowsFields($data,$allowFields=[])
    {
        $return = $temp = [];
        if (!is_array($allowFields)) {
            $allowFields = (array) $allowFields;
        }

        foreach($data as $key => $commodity) {
            foreach ($commodity as $cKey => $value) {
                if ( in_array($cKey,$allowFields) ) {
                    $temp[$cKey] = $value;
                }
            }
            $return[$key] = $temp;
        }

        return $return;
    }

}