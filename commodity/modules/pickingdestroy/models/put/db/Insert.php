<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\pickingdestroy\models\put\db;

use common\ActiveRecord\PickingDestroyAR;
use Yii;

class Insert extends PickingDestroyAR {

    //添加
    public static function insertPickingDestroy(array $data) {
        
        try {
            $Insert = new self;

            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }

         return   $Insert->save(false);
             
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   //验证数据存在不存在
    public static function getField(array $condition, $field) {
        
        return PickingDestroyAR::find()->select($field)->where($condition)->one();
        
    }

}
