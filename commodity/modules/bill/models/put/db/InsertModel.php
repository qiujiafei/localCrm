<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 11:04
 */

namespace commodity\modules\bill\models\put\db;

use common\ActiveRecord\BillAR;
use commodity\modules\bill\models\put\db\InsertBillService;
use commodity\modules\bill\models\put\db\InsertBillPicking;
use commodity\modules\picking\models\put\db\Insert as picking_insert;
use commodity\modules\damaged\models\put\db\Insert as damaged_insert;
use commodity\modules\commoditybatch\models\modify\db\Update;
use commodity\modules\pickingcommodity\models\put\db\Insert as pickingcommodity;
use commodity\modules\customerinfomation\models\modify\db\Update as Customer;
use commodity\modules\bill\models\put\db\InsertFinanceTurnover;
use commodity\modules\customerinfomation\models\put\db\Insert as CustomerInsert;
use commodity\modules\service\models\put\db\Insert as ServiceInsert;
use commodity\modules\customerinfomation\models\put\db\InsertCustomerCars;
use Yii;

class InsertModel extends BillAR {

    //开单
    public static function insertBill(array $data) {


        $picking_commodity = array_key_exists('picking_commodity', $data) ? $data['picking_commodity'] : array();
        $service_info = $data['service_info'];
        unset($data['picking_commodity']);
        unset($data['service_info']);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $status = $data['status']; //状态 默认1 (0:挂单 1:结算 2:其他)
            $Insert = new self;


            foreach ($data as $k => $v) {
                $Insert->$k = $v;
            }
            if ($Insert->save(false)) {

                $finance_turnover['bill_id'] = $bill_service['bill_id'] = $Insert->id;
                $picking_condition['store_id'] = $bill_service['store_id'] = $data['store_id'];
                $bill_service['created_by'] = $bill_service['last_modified_by'] = $data['created_by'];
                $bill_service['created_time'] = $bill_service['last_modified_time'] = $data['created_time'];

                $finance_turnover['created_by'] = $finance_turnover['last_modified_by'] = $data['created_by'];
                $finance_turnover['created_time'] = $finance_turnover['last_modified_time'] = $data['created_time'];
                $customer_car_condition['customer_infomation_id'] = $finance_turnover['customer_id'] = $customer_condition['id'] = $data['customer_infomation_id'];
                $customer_info = CustomerInsert::getField($customer_condition, 'is_member');
                $finance_turnover['is_member'] = $customer_info->is_member;
                $customer_car = InsertCustomerCars::getField($customer_car_condition, 'id');
                if(!$customer_car){
                    throw new \Exception('客户车辆信息有误', 19010);
                }
                $finance_turnover['car_id'] = $customer_car->id;
                //服务单据
                foreach ($service_info as $key => $value) {
                    $bill_service['service_id'] = $value['service_id'];
                    $bill_service['quantity'] = $value['quantity'];
                    if (InsertBillService::insertService($bill_service) === false) {
                        $transaction->rollback();
                        return false;
                    }
                    $service_condition['id'] = $finance_turnover['service_id'] = $value['service_id'];
                    $service_info = ServiceInsert::getField($service_condition, 'price');
                    $finance_turnover['price'] = $service_info->price * $value['quantity'];

                    //服务财务数据营业额
                    if (InsertFinanceTurnover::insertInsertFinance($finance_turnover) === false) {
                        $transaction->rollback();
                        return false;
                    }
                }
                //领料单据
                $bill_picking_data['store_id'] = $picking_data['store_id'] = $data['store_id'];
                $picking_data['picking_by'] = $data['created_by'];
                $picking_data['created_by'] = $picking_data['last_modified_by'] = $data['created_by'];
                $picking_data['created_time'] = $picking_data['last_modified_time'] = $data['created_time'];
                $bill_picking_data['created_by'] = $bill_picking_data['last_modified_by'] = $data['created_by'];
                $bill_picking_data['created_time'] = $bill_picking_data['last_modified_time'] = $data['created_time'];

                $comment = array_key_exists('comment', $picking_commodity) ? $picking_commodity['comment'] : '';
                if (array_key_exists('comment', $picking_commodity)) {
                    unset($picking_commodity['comment']);
                }

                if ($picking_commodity) {
                    foreach ($picking_commodity as $key => $commodity) {
                        if ($key == 0) {//九大大爷商品
                            $picking_data['number'] = picking_insert::getnumber(1, 2, 10, $picking_condition);
                        } else {
                            $picking_data['number'] = picking_insert::getnumber(2, 2, 10, $picking_condition);
                        }

                        $picking_data['comment'] = $comment;

                        $picking_id = picking_insert::insertPicking($picking_data);

                        if ($picking_id) {
                            //开单领料
                            $bill_picking_data['bill_id'] = $Insert->id;
                            $bill_picking_data['picking_id'] = $picking_id;
                            if (InsertBillPicking::insertPicking($bill_picking_data) === false) {
                                $transaction->rollback();
                                return false;
                            }
                            //报损商品添加
                            foreach ($commodity as $tab => $goods) {
                                foreach ($goods as $field => $val) {
                                    $commodity[$tab]['store_id'] = $data['store_id'];
                                    $commodity[$tab]['picking_id'] = $picking_id;
                                    $commodity[$tab]['last_modified_by'] = $data['last_modified_by'];
                                    $commodity[$tab]['last_modified_time'] = $data['last_modified_time'];
                                    $commodity[$tab]['created_time'] = $data['created_time'];
                                    $commodity[$tab]['created_by'] = $data['created_by'];
                                }
                                if ($status == 1) {
                                    //结算时更改库存
                                    $modify_batch_condition['id'] = $goods['commodity_batch_id'];
                                    $modify_batch['stock'] = -abs($goods['quantity']);
                                    $modify_stock = Update::modifyStock($modify_batch_condition, $modify_batch);
                                    if (!$modify_stock) {
                                        $transaction->rollback();
                                        return false;
                                    }
                                }
                            }
                            $field_key = array_keys(current($commodity));
                            $insert_picking_commodity = pickingcommodity::batchInsertPickingCommodity($field_key, $commodity);

                            if (!$insert_picking_commodity) {
                                $transaction->rollback();
                                return false;
                            }
                        } else {
                            $transaction->rollback();
                            return false;
                        }
                    }
                }
            } else {
                $transaction->rollback();
                return false;
            }
        
            if ($status == 1) {
                $modify_customer_condition['store_id'] = $data['store_id'];
                $modify_customer_condition['id'] = $data['customer_infomation_id'];
                $modify_customer['consume_count'] = 1; //消费次数
                $modify_customer['total_consume_price'] = array_key_exists('final_price', $data) ? $data['final_price'] : 0; //累计消费
                if (!Customer::modifyConsumeTimes($modify_customer_condition, $modify_customer)) {
                    $transaction->rollback();
                    return false;
                }
            }

            $transaction->commit();
            return [];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    //验证数据存在不存在
    public static function getField(array $condition, $field) {

        return BillAR::find()->select($field)->where($condition)->one();
    }

}
