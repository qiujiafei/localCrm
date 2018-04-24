<?php
namespace common\validators\district;

use common\models\Validator;
use common\ActiveRecord\DistrictProvinceAR;

class ProvinceValidator extends Validator{

    public $message;

    protected function validateValue($provinceId){
        return DistrictProvinceAR::findOne($provinceId) ? true : $this->message;
    }
}
