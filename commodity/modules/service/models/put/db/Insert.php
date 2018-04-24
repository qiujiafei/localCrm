<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\put\db;

use common\ActiveRecord\ServiceAR;
use Yii;

class Insert extends ServiceAR {


    //添加
    public static function insertService(array $data) {
        
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
        
        return ServiceAR::find()->select($field)->where($condition)->one();
        
    }

}
