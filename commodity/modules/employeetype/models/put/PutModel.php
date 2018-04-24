<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeetype\models\put;

use common\models\Model as CommonModel;
use commodity\modules\employeetype\models\put\db\Insert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $name; //工种名 string
    public $comment; //备注 string
    public $token;

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'name', 'comment', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['name', 'token'],
                'required',
                'message' => 2004,
            ],
            [
                ['name'],
                'string',
                'length' => [1, 30],
                'tooShort' => 6003,
                'tooLong' => 6001,
            ],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 6002],
        ];
    }

    public function actionInsert() {
        try {

            $post_data['name'] = $this->name;
            $post_data['comment'] = $this->comment;

            //整理参数
            $add_employeetype_data = self::prepareData($post_data);
//            print_r($add_employeetype_data);die;
            //添加操作
            Insert::insertEmployeeType($add_employeetype_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 6004) {
                $this->addError('insert', 6004);
                return false;
            } else {
                $this->addError('insert', 6000);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //生成employeetype参数

        $employeetype_data['name'] = $name = array_key_exists('name', $data) ? $data['name'] : '';

        $employeetype_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';

        $employeetype_data['store_id'] = $condition['store_id'] = current($userIdentity)['store_id'];

        //判断工种名是否重复
        if ($name) {
            $condition['name'] = $name;
            $info = Insert::getField($condition, 'id');
            $id = array_key_exists('id', $data) ? $data['id'] : 0;
            if ($info) {
                if (($info->id != $id && !$switch) || ($info && $switch)) {
                    throw new \Exception('工种名称已经存在', 6004);
                }
            }
        }

        $employeetype_data['last_modified_by'] = current($userIdentity)['id'];
        $employeetype_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $employeetype_data['created_time'] = date('Y-m-d H:i:s');
            $employeetype_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($employeetype_data, function ($v) {
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
