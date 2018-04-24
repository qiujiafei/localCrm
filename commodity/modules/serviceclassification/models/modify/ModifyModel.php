<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceclassification\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\serviceclassification\models\modify\db\Update;
use commodity\modules\serviceclassification\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';

    public $classification_name; //分类名称
    public $token;
    public $id;
    public $status;
    public $comment;
    public $parent_id;

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
            ['classification_name', 'string', 'length' => [0, 30], 'tooLong' => 9011],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 9012],
            ['parent_id', 'integer', 'message' => 4009],
        ];
    }

    public function actionModify() {

        try {

            $post_data['classification_name'] = $this->classification_name;
            $post_data['parent_id'] = $this->parent_id;
            $post_data['comment'] = $this->comment;
            $post_data['status'] = $this->status;
            $post_data['id'] = $this->id;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);

           

            $condition['id'] = $this->id;
            $condition['store_id'] = $modify_data['store_id'];
       
            //更改操作
            if (!Update::modifyServiceClassification($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 9013) {
                $this->addError('modify', 9013);
                return false;
            } elseif ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            }elseif ($ex->getCode() === 4004) {
                $this->addError('modify', 4004);
                return false;
            } elseif ($ex->getCode() === 4008) {
                $this->addError('modify', 4008);
                return false;
            } elseif ($ex->getCode() === 4012) {
                $this->addError('insert', 4012);
                return false;
            }  elseif ($ex->getCode() === 4013) {
                $this->addError('insert', 4013);
                return false;
            } elseif ($ex->getCode() === 4014) {
                $this->addError('insert', 4014);
                return false;
            }else {
                $this->addError('modify', 9042);
                return false;
            }
        }
    }

}
