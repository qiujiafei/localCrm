<?php
namespace common\validators\category;

use common\models\Validator;
use common\models\parts\ProductCategory;

class ParentIdValidator extends Validator{

    public $message;

    protected function validateValue($parentId){
        return ProductCategory::isParentCategory($parentId) ? : $this->message;
    }
}
