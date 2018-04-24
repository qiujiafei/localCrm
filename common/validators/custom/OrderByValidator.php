<?php
/**
 * Created by PhpStorm.
 * User: lishuang
 * Date: 2017/9/18
 * Time: 下午8:08
 */
namespace common\validators\custom;

use common\models\Validator;
class OrderByValidator extends Validator{

    public $message;
    protected function validateValue($orderBy){
        if (!is_array($orderBy) || current(array_map(function($key){
                return in_array($key,['sales','max_price']) ? false : true;
            },array_keys($orderBy)))) return $this->message;
        return true;
    }
}
