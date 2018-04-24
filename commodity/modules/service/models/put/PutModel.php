<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\put;

use common\models\Model as CommonModel;
use commodity\modules\service\models\put\db\Insert;
use commodity\modules\service\models\put\db\Get;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $service_name; //服务项目名称
    public $specification; //规格
    public $price;     //售价
    public $type;     //类型默认0 (0:非自助, 1:自助)
    public $service_claasification_id;     //服务项目分类ID
    public $status;     //状态默认1 (0:停用 1:正常)
    public $comment; //备注

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'service_name', 'specification', 'price', 'type', 'service_claasification_id', 'status', 'comment', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                [ 'service_name', 'service_claasification_id', 'token'],
                'required',
                'message' => 2004,
            ],
            ['service_name', 'string', 'length' => [1, 30], 'tooShort' => 9017, 'tooLong' => 9018],
            ['specification', 'string', 'length' => [1, 30], 'tooShort' => 9026, 'tooLong' => 9027],
            [['price'], 'double', 'min' => 0, 'tooSmall' => 9003, 'message' => 9004],
            ['price', 'string', 'length' => [0, 30], 'tooLong' => 9005],
            ['service_claasification_id', 'integer', 'min' => 1, 'tooSmall' => 9019, 'message' => 9019],
            [['type', 'status'], 'in', 'range' => [0, 1], 'message' => 9020],
            ['type', 'default', 'value' => 0],
            ['status', 'default', 'value' => 1],
            ['comment', 'string', 'length' => [0, 100], 'tooLong' => 9021],
        ];
    }

    public function actionInsert() {
        try {

            $post_data['service_name'] = $this->service_name;
            $post_data['specification'] = $this->specification;
            $post_data['price'] = $this->price;
            $post_data['type'] = $this->type;
            $post_data['service_claasification_id'] = $this->service_claasification_id;
            $post_data['status'] = $this->status;
            $post_data['comment'] = $this->comment;

            //整理参数
            $add_data = self::prepareData($post_data);
//            print_r($add_data);
//            die;
            //添加操作
            Insert::insertService($add_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 9028) {
                $this->addError('insert', 9028);
                return false;
            } elseif ($ex->getCode() === 9022) {
                $this->addError('insert', 9022);
                return false;
            } elseif ($ex->getCode() === 9023) {
                $this->addError('insert', 9023);
                return false;
            } else {
                $this->addError('insert', 9029);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成employeetype参数

        $service_data['service_name'] = $service_name = array_key_exists('service_name', $data) ? $data['service_name'] : '';

        $service_data['specification'] = array_key_exists('specification', $data) ? $data['specification'] : '';



        $service_data['price'] = array_key_exists('price', $data) ? round($data['price'], 2) : 0.00;

        $service_data['type'] = array_key_exists('type', $data) ? $data['type'] : 0;
        //获取服务项目分类名称 
        $service_data['service_claasification_id'] = $service_claasification_id = array_key_exists('service_claasification_id', $data) ? $data['service_claasification_id'] : '';

        if ($service_claasification_id) {
            $claasification_condition['id'] = $service_claasification_id;
            $claasification_condition['status'] = 1;
            $claasification_condition['store_id'] = current($userIdentity)['store_id'];
            $claasification_info = Get::getField($claasification_condition, 'classification_name');
            if (!$claasification_info) {
                throw new \Exception('服务项目分类ID参数有误', 9022);
            }
            $service_data['service_claasification_name'] = $claasification_info['classification_name'];
        }


        $service_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';

        $service_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';

        $service_data['store_id'] = $store_id = $condition['store_id'] = current($userIdentity)['store_id'];

        $id = array_key_exists('id', $data) ? $data['id'] : '';
        //服务项目名称是否重复
        if ($service_name) {
            $condition['service_name'] = $service_name;
            $info = Insert::getField($condition, 'id');
            if ($info) {
                if (($info->id != $id && !$switch) || ($info && $switch)) {
                    throw new \Exception('服务项目名称已存在', 9028);
                }
            }
        }

        //8位编码
        $service_data['service_code'] = $service_code = array_key_exists('service_code', $data) ? $data['service_code'] : '';

        if (!$service_code && $switch) {
            $service_data['service_code'] = self::getServiceCode(8, $condition);
        } else {
            $code_condition['service_code'] = $service_code;
            $code_condition['store_id'] = $store_id;
            $info = Insert::getField($code_condition, 'id');
            if ($info['id'] != $data['id'] && $info) {
                throw new \Exception('该服务项目编号已存在', 9023);
            }
        }

        $service_data['last_modified_by'] = current($userIdentity)['id'];
        $service_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $service_data['created_time'] = date('Y-m-d H:i:s');
            $service_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($service_data, function ($v) {
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

    /**
     * 生成编码
     * 
     * $len  生成服务项目编码的长度
     */
    public static function getServiceCode($len = 8, $condition) {

        $condition['service_code'] = $service_code = rand(pow(10, $len - 1), pow(10, $len) - 1);

        $info = Insert::getField($condition, 'service_code');
        if ($info) {
            $service_code = self::getServiceCode($len, $condition);
        }

        return (string) $service_code;
    }

}
