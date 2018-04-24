<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\put;

use common\models\Model as CommonModel;
use commodity\modules\bill\models\put\db\Insert;
use commodity\modules\customerinfomation\models\put\db\Insert as customerinfomation_insert;
use commodity\modules\employee\models\put\db\Insert as employee_insert;
use commodity\modules\service\models\put\db\Insert as service_insert;
use commodity\modules\bill\models\put\db\InsertModel;
use commodity\modules\damaged\models\put\PutModel as damaged_put;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $status; //默认1 (0:挂单 1:结算)
    public $customer_infomation_id;   //客户资料ID
    public $technician_id;          //技师
    public $member_discount;         //会员优惠
    public $comment;       //备注
    public $picking_commodity; //领料
    public $service_info; //服务

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'customer_infomation_id', 'technician_id', 'member_discount', 'comment', 'picking_commodity', 'service_info', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['customer_infomation_id', 'technician_id', 'service_info', 'token'],
                'required',
                'message' => 2004
            ],
            [['member_discount'], 'double', 'min' => 0, 'tooSmall' => 19000, 'message' => 19000],
            ['member_discount', 'string', 'length' => [0, 10], 'tooLong' => 19001],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 19002],
            [['status'], 'default', 'value' => 1],
        ];
    }

    /**
     * 开单
     *  picking_commodity; //领料
     *               *      commodity_batch_id  商品批次ID  
     *                      quantity            数量
     *                      comment             备注
     *  service_info; //服务
     *                      service_id
     *                      quantity            数量
     */
    public function actionInsert() {

        try {
//            $picking_commodity[0]['commodity_batch_id']=9;
//            $picking_commodity[0]['quantity']=1;
//            $picking_commodity[0]['comment']=1;
//            $picking_commodity[1]['commodity_batch_id']=10;
//            $picking_commodity[1]['quantity']=1;
//            $picking_commodity[1]['comment']=1;
//            $this->status=0;
//            $this->picking_commodity=$picking_commodity;
            $post_data['customer_infomation_id'] = $this->customer_infomation_id;
            $post_data['technician_id'] = $this->technician_id;
            $post_data['member_discount'] = $this->member_discount;
            $post_data['comment'] = $this->comment;
            $post_data['picking_commodity'] = $this->picking_commodity;
            $post_data['service_info'] = $this->service_info;

            

            //整理参数
            $add_data = self::prepareData($post_data);
            $add_data['status'] = $this->status == 1 ? 1 : 0; //默认结算
            
            //添加操作
            InsertModel::insertBill($add_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 19003) {
                $this->addError('insert', 19003);
                return false;
            } elseif ($ex->getCode() === 19004) {
                $this->addError('insert', 19004);
                return false;
            } elseif ($ex->getCode() === 19005) {
                $this->addError('insert', 19005);
                return false;
            }elseif ($ex->getCode() === 19009) {
                $this->addError('insert', 19009);
                return false;
            } elseif ($ex->getCode() === 19010) {
                $this->addError('insert', 19010);
                return false;
            }elseif ($ex->getCode() === 13008) {
                $this->addError('insert', 13008);
                return false;
            } elseif ($ex->getCode() === 14007) {
                $this->addError('insert', 13007);
                return false;
            } elseif ($ex->getCode() === 14005) {
                $this->addError('insert', 13005);
                return false;
            } elseif ($ex->getCode() === 14004) {
                $this->addError('insert', 13004);
                return false;
            } elseif ($ex->getCode() === 14003) {
                $this->addError('insert', 13003);
                return false;
            } elseif ($ex->getCode() === 14002) {
                $this->addError('insert', 13002);
                return false;
            } elseif ($ex->getCode() === 14009) {
                $this->addError('insert', 14009);
                return false;
            } elseif ($ex->getCode() === 14011) {
                $this->addError('insert', 14011);
                return false;
            } elseif ($ex->getCode() === 14012) {
                $this->addError('insert', 14012);
                return false;
            } elseif ($ex->getCode() === 14013) {
                $this->addError('insert', 14013);
                return false;
            } else {
                $this->addError('insert', 19006);
                return false;
            }
        }
    }

    /**
     * $switch  分类添加和分类更改的参数整合开关  默认返回添加的数据
     */
    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成bill参数

        $bill_data['customer_infomation_id'] = $customer_infomation_id = array_key_exists('customer_infomation_id', $data) ? $data['customer_infomation_id'] : '';

        $bill_data['store_id'] = $store_id = current($userIdentity)['store_id'];

        $member_discount = 0;
        if ($customer_infomation_id) {
            $customer_infomation_condition['id'] = $customer_infomation_id;
            $customer_infomation_condition['status'] = 1;
            $customer_infomation_condition['store_id'] = $store_id;
            $customer_infomation_info = customerinfomation_insert::getField($customer_infomation_condition, 'id,is_member');
            if (!$customer_infomation_info) {
                throw new \Exception('客户信息有误', 19003);
            }
            if ($customer_infomation_info->is_member == 1) {
                $bill_data['member_discount'] = $member_discount = array_key_exists('member_discount', $data) ? $data['member_discount'] : 0;
                $bill_data['is_member'] = 1;
            }
        }

        $bill_data['technician_id'] = $technician_id = array_key_exists('technician_id', $data) ? $data['technician_id'] : '';
        if ($technician_id) {
            $employee_condition['id'] = $technician_id;
            $employee_condition['status'] = 0;
            $employee_condition['store_id'] = $store_id;
            $employee_info = employee_insert::getField($employee_condition, 'id');
            if (!$employee_info) {
                throw new \Exception('技师信息有误', 19004);
            }
        }

        $bill_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';

        //服务
        $bill_price = 0;
        $bill_data['service_info'] = $service_all = array_key_exists('service_info', $data) ? $data['service_info'] : '';
        if (!empty($service_all) && count($service_all) > 0 && is_array($service_all)) {
            foreach ($service_all as $k => $service) {
                if (!is_array($service)) {
                    throw new \Exception('服务项目信息有误', 19005);
                }
                $service_id = array_key_exists('service_id', $service) ? $service['service_id'] : '';

                if (!$service_id) {
                    throw new \Exception('服务项目信息有误', 19005);
                }

                $quantity = array_key_exists('quantity', $service) ? $service['quantity'] : '';

                if ($quantity <= 0 || !is_int($quantity + 0)) {
                    throw new \Exception('服务数量必须为大于0的整数', 14012);
                }

                $service_all_array[] = $service_id;

                if (count($service_all_array) != count(array_unique($service_all_array))) {
                    throw new \Exception('不允许添加重复服务项目', 14013);
                }

                $service_condition['id'] = $service['service_id'];
                $service_condition['store_id'] = $store_id;
                $service_info = service_insert::getField($service_condition, 'id,price');
                if (!$service_info) {
                    throw new \Exception('服务项目信息有误', 19010);
                }
                $bill_price+=$service_info->price * $quantity;
            }
        } else {
            throw new \Exception('服务项目信息有误', 19005);
        }
        $bill_data['price'] = round($bill_price, 2) > 0 ? round($bill_price, 2) : 0;
        if ($member_discount) {
            if($member_discount>$bill_price){
                throw new \Exception('优惠金额不能大于开单金额', 19009);
            }
            $bill_price-=$member_discount;
        }

        //总价
        $bill_data['final_price'] = round($bill_price, 2) > 0 ? round($bill_price, 2) : 0;
        //单号
        $bill_condition['store_id'] = $store_id;
        $bill_data['bill_number'] = self::getnumber(8, $bill_condition);

        //领取商品
        $picking_commodity = array_key_exists('picking_commodity', $data) ? $data['picking_commodity'] : '';
        if ($picking_commodity) {
            $bill_data['picking_commodity'] = damaged_put::goodsSet($picking_commodity, $store_id);
            $bill_data['picking_commodity']['comment'] = array_key_exists('comment', $picking_commodity) ? $picking_commodity['comment'] : '';
        }


        $bill_data['last_modified_by'] = current($userIdentity)['id'];
        $bill_data['last_modified_time'] = date('Y-m-d H:i:s');


        if ($switch) {
            $bill_data['created_time'] = date('Y-m-d H:i:s');
            $bill_data['created_by'] = current($userIdentity)['id'];
        }

        return array_filter($bill_data, function ($v) {
                        if ($v === '' || $v === NULL) {
                            return false;
                        }
                        return true;
                    });
    }

    /**
     * 获取开单单号
     */
    public static function getnumber($len = 8, $condition) {

        $time = date('Ymd');

        $random = '';
        $str = '0123456789';
        for ($i = 0; $i < $len; $i++) {
            $random .= $str[rand(0, 9)];
        }

        $bill_number = $time . $random;
        $bill_condition['bill_number'] = $bill_number;
        $bill_condition['store_id'] = $condition['store_id'];
        $bill_info = InsertModel::getField($bill_condition, 'id');
        if ($bill_info) {
            $bill_number = self::getnumber(8, $condition);
        }
        return $bill_number;
    }

    public static function verifyUser() {
        if (!$userIdentity = self::getUser()) {
            throw new \Exception(sprintf(
                    "Can not found user identity in %s.", __METHOD__
            ));
        }
        return $userIdentity;
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

}
