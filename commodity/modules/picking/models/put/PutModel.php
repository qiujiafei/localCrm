<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\picking\models\put;

use common\models\Model as CommonModel;
use commodity\modules\picking\models\put\db\Insert;
use commodity\modules\picking\models\put\db\GetCommodity;
use commodity\modules\picking\models\put\db\GetCommodityStock;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $picking_by; //领料人ID
    /**
     * 领取商品的集合
    * $commodity_gather    
     *                      commodity_batch_id  商品批次ID  
     *                      quantity            数量
     *                      comment             备注
     */
    public $commodity_gather;
    public $status;  //领料单状态 默认0(0)

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'commodity_gather', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [  'commodity_gather', 'token'],
                'required',
                'message' => 2004,
            ],
            ['picking_by', 'integer', 'message' => 13000],
            [['status'], 'default', 'value' => 0],
        ];
    }

    /**
     * 添加领料,报损,退货模块接口
     * @params 
     *          $commodity_id; //商品id
     *          $count; //数量
     *          $type; //'状态,默认1,(1:领料,2.报损,3.退货)
     *          $status;  //状态,默认1(正常),其他状态未定
     * 
     *
     */
    public function actionInsert() {
        try {
            
//            
//            $post_data['picking_by'] = $this->picking_by;
            $post_data['commodity_gather'] = $this->commodity_gather;
            Insert::insertPicking($post_data);
////            $post_data['status'] = $this->status;
//            
//            //整理参数
//            $add_data = self::prepareData($post_data);
//
//            print_r($add_data);die;
//            //操作
//            if ($this->type == 1) {
//                //1:领料
//                Insert::insertCommodityUsed($add_data);
//            } elseif ($this->type == 2) {
//                //2.报损
//                Insert::insertCommodityUsed($add_data);
//            } elseif ($this->type == 3) {
//                //3.退货
//                Insert::insertCommodityUsed($add_data);
//            }
//

            return [];
        } catch (\Exception $ex) {
          if ($ex->getCode() === 14001) {
                $this->addError('insert', 13001);
                return false;
            } elseif ($ex->getCode() === 14002) {
                $this->addError('insert', 13002);
                return false;
            } elseif ($ex->getCode() === 14003) {
                $this->addError('insert', 13003);
                return false;
            } elseif ($ex->getCode() === 14004) {
                $this->addError('insert', 13004);
                return false;
            } elseif ($ex->getCode() === 14005) {
                $this->addError('insert', 13005);
                return false;
            } elseif ($ex->getCode() === 14007) {
                $this->addError('insert', 13007);
                return false;
            } elseif ($ex->getCode() === 13008) {
                $this->addError('insert', 13008);
                return false;
            } else {
                $this->addError('insert', 13006);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成picking参数
        $picking_data['picking_by'] = $type = array_key_exists('picking_by', $data) ? $data['picking_by'] : '';


        $picking_data['store_id'] = $condition['store_id'] = $store_id = current($userIdentity)['store_id'];

        $commodity_gather = array_key_exists('commodity_gather', $data) ? $data['commodity_gather'] :array();

        //测试数据
//        $commodity_gather[1]['commodity_id'] = 2;
//        $commodity_gather[1]['depot_id'] = 1;
//        $commodity_gather[1]['quantity'] = 2;
//        $commodity_gather[1]['unit_id'] = 1;
//        $commodity_gather[1]['comment'] = 'dfdasfdsf';
//        $commodity_gather[1]['status'] = 0;
        //判断领料的商品
        if (is_array($commodity_gather) && !empty($commodity_gather)) {
            foreach ($commodity_gather as $key => $commodity) {
                foreach ($commodity as $field => $value) {
                    $commodity_stock_condition['commodity_id'] = $commodity_condition['commodity_id'] = $value['commodity_id'];
                    $commodity_stock_condition['store_id'] = $commodity_condition['store_id'] = $store_id;
                    $commodity_stock_field = 'stock';
                    $commodity_stock_info = GetCommodityStock::getField($commodity_stock_condition, $commodity_stock_field);
                    if ($commodity_stock_info) {
                        if ($commodity_stock_info['stock'] < $value['quantity']) {
                            $commodity_condition['status'] = 1;
                            $commodity_field = 'price,unit_id';
                            $commodity_info = GetCommodity::getField($commodity_condition, $commodity_field);
                            if ($commodity_info) {
                                $price = $commodity_info['price'];
                                $commodity_gather[$key]['cost_price'] = $price;
                                $commodity_gather[$key]['total_price'] = $price * $value['quantity'];
                                $commodity_gather[$key]['store_id'] = $store_id;
                                $commodity_gather[$key]['status'] = array_key_exists('status', $value) ? $value['status'] : 0;
                                if(array_key_exists('comment', $value) && $value['comment']){
                                    $comment_len=mb_strlen($value['comment'],'utf8');
                                    if($comment_len>100){
                                        throw new \Exception('备注不能超过100个字符', 1111);
                                    }
                                }
                            } else {
                                throw new \Exception('您好,领取商品中有未知商品', 13002);
                            }
                        } else {
                            throw new \Exception('您好,该商品的库存超出了您的领取范围', 13001);
                        }
                    } else {
                        throw new \Exception('您好,该商品的库存超出了您的领取范围', 13001);
                    }
                }
            }
        }
        
        $picking_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';


        //$type 1:领料,2.报损,3.退货
        if ($type == 1) {
            $commodity_sum = Get::getSum($condition, 'sum');
            if ($count > $commodity_sum) {
                throw new \Exception('您好,该商品的库存超出了您的领取范围', 1111);
                return false;
            }
        }

        $picking_data['last_modified_by'] = current($userIdentity)['id'];
        $picking_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $picking_data['created_time'] = date('Y-m-d H:i:s');
            $picking_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($picking_data, function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });
    }

    /**
     * 获取编号
     * $source  来源：01为9大爷商品的领料单； 02为门店自己添加
     * $type    类型：01为采购单，02为领料单。03为报损单，04为退货单，05为盘点单。 
     * 规则：8位时间+2位来源+2位类型+10位随机数字 不允许重复 
     */
    public static function getnumber($source, $type, $len = 10) {

        $time = date('Ymd');

        $random = '';
        $str = '0123456789';
        for ($i = 0; $i < $len; $i++) {
            $random .= $str[rand(0, 9)];
        }

        return $time . '0' . $source . '0' . $type . $random;
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
