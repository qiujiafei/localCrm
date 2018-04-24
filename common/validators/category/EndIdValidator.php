<?php
namespace common\validators\category;

use common\models\Validator;
use common\models\parts\ProductCategory;

class EndIdValidator extends Validator{

    public $message;

    protected function validateValue($endId){
        return ProductCategory::existEndCategory($endId) ? true : $this->message;
    }
}
