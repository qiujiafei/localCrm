<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\put;

use common\models\Model as CommonModel;
use commodity\modules\damaged\models\put\db\Insert;
use commodity\modules\damaged\models\put\db\GetCommodity;
use commodity\modules\damaged\models\put\db\GetCommodityBatch;
use commodity\modules\damaged\models\put\db\GetSupplier;
use commodity\modules\employee\models\put\db\Insert as EmployeeInsert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $damaged_by; //报损人ID
    public $comment;

    /**
     * 报损商品的集合
     * $commodity_gather    
     *                      commodity_batch_id  商品批次ID  
     *                      quantity            数量
     *                      comment             备注
     */
    public $commodity_gather;
    public $status;  //报损单状态 默认0(0)

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'commodity_gather', 'comment', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'commodity_gather', 'token'],
                'required',
                'message' => 2004,
            ],
            ['damaged_by', 'integer', 'message' => 14000],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 14010],
        ];
    }

    /**
     * 添加报损模块接口
     */
    public function actionInsert() {
        try {

//            $post_data['damaged_by'] = $this->damaged_by;
            $post_data['commodity_gather'] = $this->commodity_gather;
            $post_data['comment'] = $this->comment;
            $post_data['status'] = 0;
            
            //整理参数
            $add_data = self::prepareData($post_data);

            //操作
            Insert::insertDamaged($add_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 14001) {
                $this->addError('insert', 14001);
                return false;
            } elseif ($ex->getCode() === 14002) {
                $this->addError('insert', 14002);
                return false;
            } elseif ($ex->getCode() === 14003) {
                $this->addError('insert', 14003);
                return false;
            } elseif ($ex->getCode() === 14004) {
                $this->addError('insert', 14004);
                return false;
            } elseif ($ex->getCode() === 14005) {
                $this->addError('insert', 14005);
                return false;
            } elseif ($ex->getCode() === 14007) {
                $this->addError('insert', 14007);
                return false;
            } elseif ($ex->getCode() === 14011) {
                $this->addError('insert', 14011);
                return false;
            } elseif ($ex->getCode() === 13008) {
                $this->addError('insert', 13008);
                return false;
            } elseif ($ex->getCode() === 14009) {
                $this->addError('insert', 14009);
                return false;
            } else {
                $this->addError('insert', 14006);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();
        $damaged_data['store_id'] = $condition['store_id'] = $store_id = current($userIdentity)['store_id'];

        //生成damaged参数
//        $damaged_data['damaged_by'] = $damaged_by = array_key_exists('damaged_by', $data) ? $data['damaged_by'] : '';
//        //报损人判断
//        if ($damaged_by) {
//            $employee_condition = self::verifierEmployee($damaged_by, $store_id);
//            if (!$employee_condition) {
//                throw new \Exception('请选择报损人', 14001);
//            }
//        } else {
//            throw new \Exception('请选择报损人', 14001);
//        }
        //报损单品库存判断
        $commodity_gather = array_key_exists('commodity_gather', $data) ? $data['commodity_gather'] : array();
//        $commodity_gather=array();
//        //测试数据
//        $commodity_gather[1]['commodity_batch_id'] = 374;
//        $commodity_gather[1]['quantity'] = 1;
//        $commodity_gather[1]['comment'] = 'abcd';
////
//        $commodity_gather[0]['commodity_batch_id'] = 374;
//        $commodity_gather[0]['quantity'] = 1;
//        $commodity_gather[0]['comment'] = 'dfdasfdsf';
        ;
        //判断报损单品
        $damaged_data['commodity_gather'] = self::goodsSet($commodity_gather, $store_id);
       
        $damaged_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';
        $damaged_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';
        $damaged_data['last_modified_by'] = current($userIdentity)['id'];
        $damaged_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $damaged_data['created_time'] = date('Y-m-d H:i:s');
            $damaged_data['damaged_by'] = $damaged_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($damaged_data, function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });
    }

    /**
     * 验证员工是否存在
     */
    public static function verifierEmployee($employee_id, $store_id) {

        $condition['id'] = $employee_id;
        $condition['status'] = 0;
        $condition['store_id'] = $store_id;
        return EmployeeInsert::getField($condition, 'id');
    }

    /**
     * 商品集处理
     * 
     * 返回 $commodity_set  1:其他 0:九大爷
     */
    public static function goodsSet($commodity_gather_old, $store_id) {
        $commodity_set = array();
      
        foreach ($commodity_gather_old as $k => $v) {
            $commodity_gather[$k]['commodity_batch_id'] = array_key_exists('commodity_batch_id', $v) ? $v['commodity_batch_id'] : '';
            $commodity_gather[$k]['quantity'] = array_key_exists('quantity', $v) ? $v['quantity'] : '';
            $commodity_gather[$k]['comment'] = array_key_exists('comment', $v) ? $v['comment'] : '';
        }
        
        if (is_array($commodity_gather) && count($commodity_gather) > 0) {

            foreach ($commodity_gather as $key => $commodity) {
                if (empty($commodity['quantity'])) {
                    throw new \Exception('商品数量不能为0', 14009);
                }
                $commodity_gather[$key]['status'] = 0;
                $commodity_batch_id_array[] = $commodity_gather[$key]['commodity_batch_id'];

                if (count($commodity_batch_id_array) != count(array_unique($commodity_batch_id_array))) {
                    throw new \Exception('不允许添加重复商品', 13008);
                }

                $commodity_batch_condition['id'] = $commodity['commodity_batch_id'];
                $commodity_batch_condition['store_id'] = $commodity_condition['store_id'] = $store_id;
                $commodity_batch_field = 'stock,commodity_id,supplier_id,cost_price,depot_id';
                $commodity_batch_info = GetCommodityBatch::getField($commodity_batch_condition, $commodity_batch_field);

                //商品信息整合
                if ($commodity_batch_info) {
                    $commodity_gather[$key]['commodity_id'] = $commodity_condition['id'] = $commodity_batch_info['commodity_id'];
                    $quantity = $commodity['quantity']? : 0;
                    
                    if(!is_numeric($quantity)){
                        throw new \Exception('数量必须为数字类型', 14011);
                    }
                    
                    if ($quantity <= 0) {
                        throw new \Exception('要报损的商品数量不能为0', 14007);
                    }
                    if ($commodity_batch_info['stock'] >= $quantity) {
                        $commodity_condition['status'] = 1;
                        $commodity_field = 'unit_id,default_depot_id';
                        $commodity_info = GetCommodity::getField($commodity_condition, $commodity_field);
                        if ($commodity_info) {
                            $commodity_gather[$key]['unit_id'] = $commodity_info['unit_id'];
                            $commodity_gather[$key]['depot_id'] = $commodity_batch_info['depot_id'];
                            $price = $commodity_batch_info['cost_price'];
                            $commodity_gather[$key]['cost_price'] = round($price, 2);
                            $commodity_gather[$key]['total_price'] = round($price * $commodity['quantity'], 2);
                            $commodity_gather[$key]['store_id'] = $store_id;
                            $commodity_gather[$key]['status'] = array_key_exists('status', $commodity) ? $commodity['status'] : 0;

                            if (array_key_exists('comment', $commodity) && $commodity['comment']) {
                                $comment = $commodity['comment'];
                                $comment_len = mb_strlen($comment, 'utf8');
                                if ($comment_len > 100) {
                                    throw new \Exception('备注不能超过100个字符', 14005);
                                }
                                $commodity_gather[$key]['comment'] = $comment;
                            }
                        } else {
                            throw new \Exception('该报损商品信息有误，无法进行报损', 14004);
                        }
                    } else {
                        throw new \Exception('要报损的商品超过实际库存数', 14003);
                    }
                } else {
                    throw new \Exception('请选择报损商品', 14002);
                }
                //来源
                $supplier_condition['id'] = $commodity_batch_info['supplier_id'];
                $supplier_info = GetSupplier::getField($supplier_condition, 'type');
                $type = $supplier_info['type'] == 1 ? 1 : 0;

                //整合数组
                if ($type === 0) {
                    $commodity_set[0][$key] = $commodity_gather[$key];
                } else {
                    $commodity_set[1][$key] = $commodity_gather[$key];
                }
            }
        } else {
            throw new \Exception('请选择报损商品', 14002);
        }

        return $commodity_set;
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
