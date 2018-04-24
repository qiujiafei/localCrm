<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\put;

use common\models\Model as CommonModel;
use commodity\modules\customercarstirebrand\models\put\db\Insert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $brand_name; //品牌名 string
    public $status;  //状态,默认1

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'brand_name', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'brand_name', 'token'],
                'required',
                'message' => 2004,
            ],
            ['brand_name', 'string', 'length' => [1, 30], 'tooShort' => 15000, 'tooLong' => 15001],
            ['status', 'default', 'value' => 1],
        ];
    }

    /**
     * 添加轮胎品牌
     * 
     * @return bool 
     */
    public function actionInsert() {
        try {

            $post_data['brand_name'] = $this->brand_name;
            $post_data['status'] = $this->status;

            //整理参数
            $add_data = self::prepareData($post_data);

//            print_r($add_data);
//            die;
            //添加操作
            Insert::insertCustomerCarsTireBrand($add_data);

            return [];
        } catch (\Exception $ex) {

            if ($ex->getCode() === 15002) {
                $this->addError('insert', 15002);
                return false;
            } else {
                $this->addError('insert', 15003);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成customercarstirebrand参数

        $customercarstirebrand_data['brand_name'] = $brand_name = array_key_exists('brand_name', $data) ? $data['brand_name'] : '';

        $customercarstirebrand_data['id'] = $id = array_key_exists('id', $data) ? $data['id'] : '';

        $customercarstirebrand_data['store_id'] = $condition['store_id'] = current($userIdentity)['store_id'];

        if ($brand_name) {
            $condition['brand_name'] = $brand_name;
            $condition['status'] = 1;
            $info = Insert::getField($condition, 'id');
            if ($info) {
                if ($switch || (!$switch && $info->id != $id )) {
                    throw new \Exception('轮胎品牌名已存在', 15002);
                }
            }
        }
       
        $customercarstirebrand_data['status'] = array_key_exists('status', $data) ? $data['status'] :'';


        $customercarstirebrand_data['last_modified_by'] = current($userIdentity)['id'];
        $customercarstirebrand_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $customercarstirebrand_data['created_time'] = date('Y-m-d H:i:s');
            $customercarstirebrand_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($customercarstirebrand_data, function ($v) {
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
