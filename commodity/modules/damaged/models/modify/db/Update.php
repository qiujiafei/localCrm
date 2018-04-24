<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\modify\db;

use common\ActiveRecord\DamagedAR;
use commodity\modules\damagedcommodity\models\get\db\Select;
use commodity\modules\damaged\models\put\db\Insert;
use commodity\modules\commoditybatch\models\modify\db\Update as commoditybatch;
use commodity\modules\damaged\models\put\PutModel;
use commodity\modules\damageddestroy\models\put\db\Insert as damageddestroy_insert;
use Yii;

class Update extends DamagedAR {

    //更改
    public static function modifyDamaged(array $condition, array $modify_damaged_data) {


        try {

            $modify = DamagedAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_damaged_data as $k => $v) {
                $modify->$k = $v;
            }

            if ($modify->save() === false) {
                throw new \Exception('员工更改失败', 7014);
                return false;
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //批量作废
    public static function modifyAllDamaged(array $condition, array $modify_damaged_data, $comment) {
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $userIdentity = PutModel::verifyUser();
            if (DamagedAR::updateAll($modify_damaged_data, $condition)) {
                
                $store_id = $condition['store_id'];
                $damaged_id = $condition['id'];

                foreach ($damaged_id as $key => $value) {
                    $damaged_destroy = array();
                    $damaged_destroy['damaged_id']=$damaged_condition['id'] = $value;
                    $damaged_info = Insert::getField($damaged_condition, 'number,damaged_by,created_time');
                    $damaged_destroy['damaged_number'] = $damaged_info['number'];
                    $damaged_destroy['damaged_by'] = $damaged_info['damaged_by'];
                    $damaged_destroy['origin_created_time'] = $damaged_info['created_time'];
//                    $damaged_destroy['comment'] = $comment[$value]? : '';
                    $damaged_destroy['origin_status'] = 0;

                    $damaged_destroy['damaged_id'] = $damaged_commodity_condition['damaged_id'] = $value;
                    $damaged_commodity_condition['store_id'] = $store_id;
                    $damaged_commodity = Select::getProAll($damaged_commodity_condition, 'damaged_id,commodity_batch_id,quantity,total_price');
//                    //更改库存
                    $damaged_destroy['total_quantity'] = 0;
                    $damaged_destroy['total_price'] = 0;
                    foreach ($damaged_commodity as $key => $value) {
                        $damaged_destroy['total_quantity']+=$value['quantity'];
                        $damaged_destroy['total_price']+=$value['total_price'];
//                        $modify_batch_condition['id'] = $value['commodity_batch_id'];
//                        $modify_batch['stock'] = abs($value['quantity']);
//                        $modify_stock = commoditybatch::modifyStock($modify_batch_condition, $modify_batch);
//
//                        if (!$modify_stock) {
//                            $transaction->rollback();
//                            return false;
//                        }
                    }
                    
                    $damaged_destroy['store_id'] = $store_id;
                    $damaged_destroy['last_modified_by'] = $damaged_destroy['created_by'] = $damaged_destroy['destroy_by'] = current($userIdentity)['id'];
                    $damaged_destroy['last_modified_time'] = $damaged_destroy['created_time'] = date('Y-m-d H:i:s');
  
                    $damaged_destroy_insert = damageddestroy_insert::insertDamagedDestroy($damaged_destroy);
                   
                    if (!$damaged_destroy_insert) {
                        $transaction->rollback();
                        return false;
                    }
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollback();
                return false;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
