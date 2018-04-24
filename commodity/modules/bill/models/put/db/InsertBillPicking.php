<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\put\db;

use common\ActiveRecord\BillPickingAR;
use Yii;

class InsertBillPicking extends BillPickingAR {

    //添加
    public static function insertPicking(array $data) {

        try {
            $insert = new self;

            foreach ($data as $k => $v) {
                $insert->$k = $v;
            }

            return $insert->save(false);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return BillPickingAR::find()->select($field)->where($condition)->one();
    }

    //验证数据存在不存在
    public static function getAll(array $condition, $field) {

        return BillPickingAR::find()->select($field)->where($condition)->asArray()->all();
    }

}
