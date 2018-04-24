<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\put\db;

use common\ActiveRecord\FinanceTurnoverAR;
use Yii;

class InsertFinanceTurnover extends FinanceTurnoverAR {

    //添加
    public static function insertInsertFinance(array $data) {

        try {
            $insert = new self;

            foreach ($data as $k => $v) {
                $insert->$k = $v;
            }
            if($insert->save(false)===false){
                return false;
            }
            return $insert->id;
            
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return BillServiceAR::find()->select($field)->where($condition)->one();
    }

}
