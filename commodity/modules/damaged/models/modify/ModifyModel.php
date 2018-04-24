<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\damaged\models\modify\db\Update;
use commodity\modules\damaged\models\put\PutModel;
use commodity\modules\damaged\models\get\db\Select;

class ModifyModel extends CommonModel {

    const ACTION_INVALID = 'action_invalid';

    public $token;
    public $destroy; //array(id[必填],comment[选填])

    public function scenarios() {
        return [
            self::ACTION_INVALID => [
                'destroy'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['destroy', 'token'],
                'required',
                'message' => 2004,
            ],
        ];
    }

    //报废
    public function actionInvalid() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $destroy = $this->destroy;
            $comment = array();
            foreach ($destroy as $key => $value) {
                $id[] = $value['id'];
                $comment[$value['id']] = $value['comment']? : '';
                if ($value['comment']) {
                    $comment_len = mb_strlen($value['comment'], 'utf8');
                    if ($comment_len > 100) {
                        throw new \Exception('备注不能超过100个字符', 14005);
                    }
                }
            }

            if (!is_array($id)) {
                throw new \Exception('参数错误', 2004);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
            $condition['status'] = 0;

            //判断id
            $count = Select::getCount($condition);
            if ($count == 0 || $count != count($id)) {
                throw new \Exception('参数错误', 2004);
            }

            $modify_damaged_data['status'] = 1;
            //更改操作
            if (Update::modifyAllDamaged($condition, $modify_damaged_data, $comment) === false) {
                throw new \Exception('报损单报废失败', 14008);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 2004) {
                $this->addError('invalid', 2004);
                return false;
            } elseif ($ex->getCode() === 14005) {
                $this->addError('invalid', 14005);
                return false;
            } else {
                $this->addError('invalid', 14008);
                return false;
            }
        }
    }

}
