<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeetype\models\put\db;

use common\ActiveRecord\EmployeeTypeAR;
use Yii;

class Insert extends EmployeeTypeAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    //添加
    public static function insertEmployeeType(array $data) {
        
        try {
            $unitInsert = new self;

            foreach ($data as $k => $v) {
                $unitInsert->$k = $v;
            }

            $unitInsert->save(false);
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return EmployeeTypeAR::find()->select($field)->where($condition)->one();
    }

}
