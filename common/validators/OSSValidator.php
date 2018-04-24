<?php
/**
 * Created by PhpStorm.
 * User: qingqiu
 * Date: 17-10-30
 * Time: 下午4:20
 */

namespace common\validators;


use common\ActiveRecord\OSSUploadFileAR;
use yii\validators\Validator;

class OSSValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $files = (array)$model->$attribute;
        $num = OSSUploadFileAR::find()->where(['filename' => $files])->count();
        if($num != count($files)){
            $this->addError($model, $attribute, 5390);
        }
    }
}