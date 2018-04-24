<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\put\db;

use common\ActiveRecord\ServiceAdditionAR;
use Yii;

class Insert extends ServiceAdditionAR {


    //添加
    public static function insertServiceAddition(array $data) {
        
        try {
            $insert = new self;

            foreach ($data as $k => $v) {
                $insert->$k = $v;
            } 
            
            $insert->save(false);
            
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return ServiceAdditionAR::find()->select($field)->where($condition)->one();
    }

}
