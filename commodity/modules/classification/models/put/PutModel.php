<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\put;

use common\models\Model as CommonModel;
use commodity\modules\classification\models\put\db\Insert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $classification_name;   //分类名称
    public $status;          //状态  默认1
    public $comment;         //备注
    public $parent_id;       //分类树父级ID
    public $depth_limit = 3; //默认三级目录

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'classification_name', 'comment', 'parent_id', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['classification_name', 'token'],
                'required',
                'message' => 2004
            ],
            ['classification_name', 'string', 'length' => [1, 30], 'tooShort' => 4011, 'tooLong' => 4001],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 4002],
            ['status', 'default', 'value' => 1],
            ['depth', 'default', 'value' => 1],
            ['parent_id', 'integer', 'message' => 4009],
        ];
    }

    /**
     * 添加分类
     */
    public function actionInsert() {

        try {

            $post_data['classification_name'] = $this->classification_name;
            $post_data['parent_id'] = $this->parent_id;
            $post_data['comment'] = $this->comment;
            $post_data['status'] = $this->status;

            //整理参数
            $add_data = self::prepareData($post_data);
//            print_r($add_data);die;
            //添加操作
            Insert::insertClassification($add_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 4003) {
                $this->addError('insert', 4003);
                return false;
            } elseif ($ex->getCode() === 4004) {
                $this->addError('insert', 4004);
                return false;
            } elseif ($ex->getCode() === 4013) {
                $this->addError('insert', 4013);
                return false;
            } elseif ($ex->getCode() === 4014) {
                $this->addError('insert', 4014);
                return false;
            } else {
                $this->addError('insert', 4000);
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

        //生成classification参数

        $classification_data['classification_name'] = $classification_name = array_key_exists('classification_name', $data) ? $data['classification_name'] : '';

        $classification_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';

        $classification_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';

        $classification_data['store_id'] = $condition['store_id'] = $store_id = current($userIdentity)['store_id'];

        $id = array_key_exists('id', $data) ? $data['id'] : 0;
        
        //顶级信息
        $condition_top['parent_id'] = -1;
        $top_info = Insert::getField($condition_top, 'depth,id');

        $parent_id = array_key_exists('parent_id', $data) ? $data['parent_id'] : '';
        if ($parent_id) {
            $classification_data['parent_id'] = $condition_parent['id'] = $parent_id = self::getParentId($parent_id);
            if ($parent_id != $top_info->id) {
                $condition_parent['store_id'] = $store_id;
            }
            $parent_info = Insert::getField($condition_parent, 'depth,id');
            if (!$parent_info || $parent_id == -1 || $id == $parent_id) {
                throw new \Exception('分类父级参数有误', 4013);
            }
            $classification_data['depth'] = self::getDepth($parent_id);
        } elseif (!$parent_id && $switch) {
            throw new \Exception('请选择父级分类,父级分类不能为空', 4014);
        }



        if ($id) {
            $condition['id'] = $id;
            $old_into = Insert::getField($condition, 'depth,id');
            if (!$old_into) {
                throw new \Exception('该分类不存在', 4012);
            }
        }
        unset($condition['id']);

        //判断分类名称是否重复

        if ($classification_name) {
            $condition['classification_name'] = $classification_name;
            $info = Insert::getField($condition, 'id');
            if ($info) {
                if (($info->id != $id && !$switch) || ($info && $switch)) {
                    throw new \Exception('分类名称重复', 4003);
                }
            }
        }

        $classification_data['last_modified_by'] = current($userIdentity)['id'];
        $classification_data['last_modified_time'] = date('Y-m-d H:i:s');


        if ($switch) {
            $classification_data['created_time'] = date('Y-m-d H:i:s');
            $classification_data['created_by'] = current($userIdentity)['id'];
        }

        return array_filter($classification_data, function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });
    }

    public static function getDepth($parent_id) {

        $condition['id'] = $parent_id;
        $condition['status'] = 1;

        $info = Insert::getField($condition, 'depth');

        if ($info) {
            $depth = $info->depth;

            if ($depth == 3) {
                throw new \Exception('您无法在三级分类下进行添加，暂时只支持三级分类', 4004);
            }

            $depth++;
        } else {
            $depth = 1;
        }

        return $depth;
    }

    public static function getParentId($parent_id) {

        $condition['id'] = $parent_id;
        $condition['status'] = 1;

        $info = Insert::getField($condition, 'id');

        if ($info) {
            $parent_id = $info->id;
        } else {
            $parent_id = -1;
        }

        return $parent_id;
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
