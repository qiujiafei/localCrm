<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\import\models\data\db;

use common\ActiveRecord\EmployeeAR;
use Yii;

class Insert extends EmployeeAR {

    //添加
    public static function insertEmployee(array $data) {
        
        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }

            $Insert->save(false);
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   //验证数据存在不存在
    public static function getField(array $condition, $field) {
        
        return EmployeeAR::find()->select($field)->where($condition)->one();
        
    }

}
