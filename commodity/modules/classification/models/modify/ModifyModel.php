<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\classification\models\modify\db\Update;
use commodity\modules\classification\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';

    public $token;
    public $classification_name;   //分类名称
    public $status;          //状态  默认1
    public $comment;         //备注
    public $parent_id;       //分类树父级ID
    public $depth_limit = 3; //默认三级目录
    public $id;  //状态,默认0,(0:在职, 1:离职)

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'classification_name', 'comment', 'parent_id', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['id', 'token'],
                'required',
                'message' => 2004
            ],
            ['classification_name', 'string', 'length' => [0, 30], 'tooLong' => 4001],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 4002],
            ['parent_id', 'integer', 'message' => 4009],
        ];
    }

    public function delEmpty($v) {
        if ($v === "" || $v === "php") {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
            return false;
        }
        return true;
    }

    /**
     * 更改分类信息
     */
    public function actionModify() {

        try {
            
            $post_data['classification_name'] = $this->classification_name;
            $post_data['parent_id'] = $this->parent_id;
            $post_data['comment'] = $this->comment;
            $post_data['id'] = $this->id;
            //整理参数
            $modify_classify_data = PutModel::prepareData($post_data, false);

            $condition['id'] = $this->id;
            $condition['store_id'] = $modify_classify_data['store_id'];

            //更改操作
            if (!Update::modifyClassification($condition, $modify_classify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {

            if ($ex->getCode() === 4003) {
                $this->addError('modify', 4003);
                return false;
            } elseif ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } elseif ($ex->getCode() === 4004) {
                $this->addError('modify', 4004);
                return false;
            } elseif ($ex->getCode() === 4008) {
                $this->addError('modify', 4008);
                return false;
            } elseif ($ex->getCode() === 4012) {
                $this->addError('modify', 4012);
                return false;
            } elseif ($ex->getCode() === 4013) {
                $this->addError('insert', 4013);
                return false;
            } else {
                $this->addError('modify', 4010);
                return false;
            }
        }
    }

}
