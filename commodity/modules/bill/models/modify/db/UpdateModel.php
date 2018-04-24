<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 15:41
 */

namespace commodity\modules\bill\models\modify\db;

use common\ActiveRecord\BillAR;
use commodity\modules\bill\models\put\db\InsertBillPicking;
use commodity\modules\bill\models\modify\db\GetPickingCommodity;
use commodity\modules\commoditybatch\models\modify\db\Update;
use commodity\modules\picking\models\modify\db\Update as picking_update;
use commodity\modules\customerinfomation\models\modify\db\Update as Customer;
use commodity\modules\bill\models\put\db\InsertModel;
use Yii;

class UpdateModel extends BillAR {

    //挂单后的结算
    public static function modifyAccountBill(array $condition, array $modify_data) {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modify = BillAR::updateAll($modify_data, $condition);

            if ($modify === false) {
                $transaction->rollback();
                return false;
            } else {
                $bill_picking_condition['store_id'] = $condition['store_id'];
                $bill_picking_condition['bill_id'] = $condition['id'];
                $bill_picking_data = InsertBillPicking::getAll($bill_picking_condition, 'picking_id');
                if ($bill_picking_data) {
                    foreach ($bill_picking_data as $key => $value) {
                        $bill_picking_commodity_condition['store_id'] = $condition['store_id'];
                        $bill_picking_commodity_condition['picking_id'] = $value['picking_id'];
                        $bill_picking_commodity = GetPickingCommodity::getAllPickingCommodity($bill_picking_commodity_condition, 'commodity_batch_id,quantity');

                        foreach ($bill_picking_commodity as $k => $val) {
                            $modify_batch_condition['id'] = $val['commodity_batch_id'];
                            $modify_batch['stock'] = -abs($val['quantity']);
                            $modify_stock = Update::modifyStock($modify_batch_condition, $modify_batch);

                            if (!$modify_stock) {
                                $transaction->rollback();
                                return false;
                            }
                        }
                    }
                }

                //消费次数
                $bill_id = $condition['id'];
                foreach ($bill_id as $v_id) {
                    $condition_bill['id'] = $v_id;
                    $bill_info = InsertModel::getField($condition_bill,'customer_infomation_id');
                    $modify_customer_condition['store_id'] = $condition['store_id'];
                    $modify_customer_condition['id'] = $bill_info->customer_infomation_id;
                    $modify_customer['consume_count'] = 1;
                    if (!Customer::modifyConsumeTimes($modify_customer_condition, $modify_customer)) {
                        $transaction->rollback();
                        return false;
                    }
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //挂单后作废
    public static function modifyInvalidBill(array $condition, array $modify_data) {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modify = BillAR::updateAll($modify_data, $condition);

            if ($modify === false) {
                $transaction->rollback();
                return false;
            } else {
                $bill_picking_condition['store_id'] = $condition['store_id'];
                $bill_picking_condition['bill_id'] = $condition['id'];
                $bill_picking_data = InsertBillPicking::getAll($bill_picking_condition, 'picking_id');
                if ($bill_picking_data) {
                    foreach ($bill_picking_data as $key => $value) {
                        $picking_condition['store_id'] = $condition['store_id'];
                        $picking_condition['id'][$key] = $value['picking_id'];
                        $picking_condition['status'] = 0;
                        $modify_picking_data['status'] = 1;
                        $modify_picking = picking_update::modifyAllPicking($picking_condition, $modify_picking_data);
                        if ($modify_picking === false) {
                            $transaction->rollback();
                            return false;
                        }
                    }
                }
            }
            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getCount(array $condition = array()) {

        return BillAR::find()->where($condition)->count();
    }

}
