<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeetype\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\employeetype\models\modify\db\Update;
use commodity\modules\employeetype\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';

    public $id;
    public $name; //工种名 string
    public $comment; //备注 string
    public $token;

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'name', 'comment', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['id', 'token'],
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

    public function actionModify() {

        try {
            
            $post_data['id'] = $this->id;
            $post_data['name'] = $this->name;
            $post_data['comment'] = $this->comment;

            //整理参数
            $modify_employeetype_data = PutModel::prepareData($post_data, false);
            
            $condition['id'] = $this->id;
            $condition['store_id'] = $modify_employeetype_data['store_id'];

            //更改操作
            if (!Update::modifyEmployeetype($condition, $modify_employeetype_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            }  elseif ($ex->getCode() === 6004) {
                $this->addError('modify', 6004);
                return false;
            } else {
                $this->addError('modify', 6005);
                return false;
            }
        }
    }

}
