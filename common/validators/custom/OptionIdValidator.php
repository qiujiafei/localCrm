<?php
/**
 * Created by PhpStorm.
 * User: lishuang
 * Date: 2017/9/18
 * Time: 下午8:08
 */
namespace common\validators\custom;

use common\models\Validator;
use Yii;
class OptionIdValidator extends Validator{

    public $message;

    protected function validateValue($optionId){
        if(gettype($optionId) != 'array') return $this->message;
        $model = Yii::$app->RQ->AR(new \common\ActiveRecord\ProductSPUOptionAR());
        array_walk_recursive($optionId, function($value) use($model){
            if(!$model->exists(['where'=>['id'=>$value]])) return $this->message;
        });
        return true;
    }
}
