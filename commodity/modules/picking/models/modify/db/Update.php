<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\picking\models\modify\db;

use common\ActiveRecord\PickingAR;
use commodity\modules\pickingcommodity\models\get\db\Select;
use commodity\modules\picking\models\put\db\Insert;
use commodity\modules\commoditybatch\models\modify\db\Update as commoditybatch;
use commodity\modules\picking\models\put\PutModel;
use commodity\modules\pickingdestroy\models\put\db\Insert as pickingdestroy_insert;
use Yii;

class Update extends PickingAR {

    //更改
    public static function modifyPicking(array $condition, array $modify_picking_data) {


        try {

            $modify = PickingAR::find()->where($condition)->one();

            if (!$modify) {
                return false;
            }

            foreach ($modify_picking_data as $k => $v) {
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
    public static function modifyAllPicking(array $condition, array $modify_picking_data, $comment= array()) {

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $userIdentity = PutModel::verifyUser();
          
            if (PickingAR::updateAll($modify_picking_data, $condition)) {

                $store_id = $condition['store_id'];
                $picking_id = $condition['id'];

                foreach ($picking_id as $key => $value) {
                    $picking_destroy = array();
                    $picking_destroy['picking_id'] = $picking_condition['id'] = $value;
                    $picking_info = Insert::getField($picking_condition, 'number,picking_by,created_time');
                    $picking_destroy['picking_number'] = $picking_info['number'];
                    $picking_destroy['picking_by'] = $picking_info['picking_by'];
                    $picking_destroy['origin_created_time'] = $picking_info['created_time'];
//                    $picking_destroy['comment'] = empty($comment)? $comment[$value]: '';
                    $picking_destroy['origin_status'] = 0;

                    $picking_destroy['picking_id'] = $picking_commodity_condition['picking_id'] = $value;
                    $picking_commodity_condition['store_id'] = $store_id;
                    $picking_commodity = Select::getProAll($picking_commodity_condition, 'picking_id,commodity_batch_id,quantity,total_price');
                    //更改库存
                    $picking_destroy['total_quantity'] = 0;
                    $picking_destroy['total_price'] = 0;
                    foreach ($picking_commodity as $key => $value) {
                        $picking_destroy['total_quantity']+=$value['quantity'];
                        $picking_destroy['total_price']+=$value['total_price'];
//                        $modify_batch_condition['id'] = $value['commodity_batch_id'];
//                        $modify_batch['stock'] = abs($value['quantity']);
//                        $modify_stock = commoditybatch::modifyStock($modify_batch_condition, $modify_batch);
//
//                        if (!$modify_stock) {
//                            $transaction->rollback();
//                            return false;
//                        }
                    }


                    $picking_destroy['store_id'] = $store_id;
                    $picking_destroy['last_modified_by'] = $picking_destroy['created_by'] = $picking_destroy['destroy_by'] = current($userIdentity)['id'];
                    $picking_destroy['last_modified_time'] = $picking_destroy['created_time'] = date('Y-m-d H:i:s');

                    $picking_destroy_insert = pickingdestroy_insert::insertPickingDestroy($picking_destroy);

                    if (!$picking_destroy_insert) {
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
