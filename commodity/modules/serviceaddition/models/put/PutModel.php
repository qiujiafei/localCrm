<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\put;

use common\models\Model as CommonModel;
use commodity\modules\serviceaddition\models\put\db\Insert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $addition_name; //项目名称
    public $price; //售价
    public $status; //预留状态默认1
    public $token;

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'addition_name', 'price', 'status', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['addition_name','price','token'],
                'required',
                'message' => 2004,
            ],
            [
                ['addition_name'],
                'string',
                'length' => [1, 30],
                'tooShort' => 9001,
                'tooLong' => 9002,
                'message' => 9039
            ],
            [['price'], 'double', 'min' => 0, 'tooSmall' => 9003, 'message' => 9004],
            ['price', 'string', 'length' => [0, 30], 'tooLong' => 9005],
            ['status', 'default', 'value' => 1],
        ];
    }

    public function actionInsert() {
        try {

            $post_data['addition_name'] = $this->addition_name;
            $post_data['price'] = $this->price;
            $post_data['status'] = $this->status;

            //整理参数
            $add_data = self::prepareData($post_data);
//            print_r($add_data);die;
            //添加操作
            Insert::insertServiceAddition($add_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 9006) {
                $this->addError('insert', 9006);
                return false;
            } else {
                $this->addError('insert', 9000);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成服务项目附加项目参数
 
        $serviceaddition_data['addition_name'] =$addition_name= array_key_exists('addition_name', $data) ? $data['addition_name'] : '';

        $serviceaddition_data['price'] = array_key_exists('price', $data) ? round($data['price'],2): '';
       
        $serviceaddition_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';
 
        $serviceaddition_data['store_id'] = $condition['store_id']= current($userIdentity)['store_id'];
        
        //判断附加服务项目名称是否重复
         //判断工种名是否重复
        if ($addition_name) {
            $condition['addition_name'] = $addition_name;
            $info = Insert::getField($condition, 'id');
            $id = array_key_exists('id', $data) ? $data['id'] : 0;
            if ($info) {
                if (($info->id != $id && !$switch) || ($info && $switch)) {
                    throw new \Exception('项目名称已经存在', 9006);
                }
            }
        }
        
        $serviceaddition_data['last_modified_by'] = current($userIdentity)['id'];
        $serviceaddition_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $serviceaddition_data['created_time'] = date('Y-m-d H:i:s');
            $serviceaddition_data['created_by'] = current($userIdentity)['id'];
        }
       

        return array_filter($serviceaddition_data, function ($v) {
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
