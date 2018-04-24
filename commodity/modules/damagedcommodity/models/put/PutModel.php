<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damagedcommodity\models\put;

use common\models\Model as CommonModel;
use commodity\modules\damagedcommodity\models\put\db\Insert;
use commodity\modules\damagedcommodity\models\put\db\Get;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $commodity_id; //商品id
    public $count; //数量
    public $type; //'状态,默认1,(1:领料,2.报损,3.退货)
    public $status;  //状态,默认1(正常),其他状态未定

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'commodity_id', 'count', 'type', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'commodity_id', 'count', 'token'],
                'required',
                'message' => 2004,
            ],
            [['commodity_id', 'count'], 'integer', 'min' => 1, 'tooSmall' => 7003, 'tooBig' => 7004],
            ['type', 'in', 'range' => [1, 2, 3], 'message' => 2005],
            [['type', 'status'], 'default', 'value' => 1],
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
     * @return bool 
     */
    public function actionInsert() {
        try {

            $post_data['commodity_id'] = $this->commodity_id;
            $post_data['count'] = $this->count;
            $post_data['type'] = $this->type;
            $post_data['status'] = $this->status;

            //整理参数
            $add_data = self::prepareData($post_data);

//            print_r($add_data);die;
            //操作
            if($this->type==1){
                //1:领料
                Insert::insertCommodityUsed($add_data);
            }elseif($this->type==2){
                //2.报损
                Insert::insertCommodityUsed($add_data);
            }elseif($this->type==3){
                //3.退货
                Insert::insertCommodityUsed($add_data);
            }
            

            return [];
        } catch (\Exception $ex) {
            $this->addError('insert', 7000);
            return false;
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成damagedcommodity参数


        $damagedcommodity_data['type'] = $type = array_key_exists('type', $data) ? $data['type'] : '';
        
        $damagedcommodity_data['commodity_id'] = $condition['commodity_id']=array_key_exists('commodity_id', $data) ? $data['commodity_id'] : '';

        $damagedcommodity_data['count']=$count=array_key_exists('count', $data) ? $data['count'] : '';


        $damagedcommodity_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';

        $damagedcommodity_data['store_id'] = $condition['store_id'] = current($userIdentity)['store_id'];

        //$type 1:领料,2.报损,3.退货
        if ($type == 1) {
             $commodity_sum=Get::getSum($condition, 'sum');
             if($count>$commodity_sum){
                 throw new \Exception('您好,该商品的库存超出了您的领取范围',1111);
                 return false;
             }
        }
        
        $damagedcommodity_data['last_modified_by'] = current($userIdentity)['id'];
        $damagedcommodity_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $damagedcommodity_data['created_time'] = date('Y-m-d H:i:s');
            $damagedcommodity_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($damagedcommodity_data, function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });
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
