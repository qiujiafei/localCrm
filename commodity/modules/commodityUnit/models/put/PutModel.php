<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\models\put;

use common\models\Model as CommonModel;
use commodity\modules\commodityUnit\models\put\db\Insert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $unit_name;
    public $status;

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'unit_name', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['unit_name', 'token'],
                'required',
                'message' => 2004
            ],
            ['status', 'default', 'value' => 1],
            ['unit_name', 'string', 'length' => [1, 10], 'tooShort' => 3009, 'tooLong' => 3002],
        ];
    }

    /**
     * 添加单位
     */
    public function actionInsert() {
        try {

            $post_data['unit_name'] = $this->unit_name;
            $post_data['status'] = $this->status;

            //整理参数
            $add_unit_data = self::prepareData($post_data);
//            print_r($add_unit_data);
//            die;
            //添加操作
            Insert::insertCommodityUnit($add_unit_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3001) {
                $this->addError('insert', 3001);
                return false;
            } else {
                $this->addError('insert', 3000);
                return false;
            }
        }
    }

    /**
     * $switch  单位添加和单位更改的参数整合开关  默认返回添加的数据
     */
    public static function prepareData(array $data, $switch = true) {
        //判断user是否存在
        $userIdentity = self::verifyUser();
        $unit_data['store_id'] = $condition['store_id'] = current($userIdentity)['store_id'];

        $unit_data['unit_name'] = $unit_name = array_key_exists('unit_name', $data) ? $data['unit_name'] : '';

        $id = array_key_exists('id', $data) ? $data['id'] : '';
        
        if ($unit_name) {
            $condition['unit_name'] = $unit_name;
            $info = Insert::getField($condition, 'id');
            if ($info) {
                if (($info->id != $id && !$switch) || ($info && $switch)) {
                    throw new \Exception('单位名称已存在', 3001);
                }
            }
        }

        $unit_data['last_modified_by'] = current($userIdentity)['id'];
        $unit_data['last_modified_time'] = date('Y-m-d H:i:s');


        if ($switch) {
            $unit_data['created_time'] = date('Y-m-d H:i:s');
            $unit_data['created_by'] = current($userIdentity)['id'];
        }

        return array_filter($unit_data, function ($v) {
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
