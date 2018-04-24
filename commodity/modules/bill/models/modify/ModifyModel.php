<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\bill\models\modify\db\UpdateModel;
use commodity\modules\bill\models\put\PutModel;

class ModifyModel extends CommonModel {
    
    const ACTION_MODIFY = 'action_modify';
    const ACTION_ACCOUNT = 'action_account';
    const ACTION_INVALID = 'action_invalid';

    public $token;
    public $id; //array(id[必填])
    
    public function scenarios() {
        return [
             self::ACTION_MODIFY => [
                'id', 'token'
            ],
            self::ACTION_ACCOUNT => [
                'id', 'token'
            ],
            self::ACTION_INVALID => [
                'id', 'token'
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
        ];
    }

    //编辑
    public function actionModify() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id = $this->id;

            if (!is_array($id)) {
                throw new \Exception('参数错误', 2004);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
            $condition['status'] = 0;

            //判断id
            $count = UpdateModel::getCount($condition);
            if ($count == 0 || $count != count($id)) {
                throw new \Exception('参数错误', 2004);
            }

            $modify_bill_data['status'] = 1;
            //更改操作
            if (UpdateModel::modifyAccountBill($condition, $modify_bill_data) === false) {
                throw new \Exception('结算失败', 19007);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 2004) {
                $this->addError('invalid', 2004);
                return false;
            } else {
                $this->addError('invalid', 19007);
                return false;
            }
        }
    }

    //挂单后结算
    public function actionAccount() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id = $this->id;

            if (!is_array($id)) {
                throw new \Exception('参数错误', 2004);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
            $condition['status'] = 0;

            //判断id
            $count = UpdateModel::getCount($condition);
            if ($count == 0 || $count != count($id)) {
                throw new \Exception('参数错误', 2004);
            }
            $userIdentity = PutModel::verifyUser();
            $modify_bill_data['last_modified_by'] = current($userIdentity)['store_id'];
            $modify_bill_data['last_modified_time'] = date('Y-m-d H:i:s');
            $modify_bill_data['status'] = 1;
            //更改操作
            if (UpdateModel::modifyAccountBill($condition, $modify_bill_data) === false) {
                throw new \Exception('结算失败', 19007);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 2004) {
                $this->addError('account', 2004);
                return false;
            } else {
                $this->addError('account', 19007);
                return false;
            }
        }
    }

    //作废开单
    public function actionInvalid() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id = $this->id;

            if (!is_array($id)) {
                throw new \Exception('参数错误', 2004);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
            $condition['status'] = 0;

            //判断id
            $count = UpdateModel::getCount($condition);
            if ($count == 0 || $count != count($id)) {
                throw new \Exception('参数错误', 2004);
            }
            $userIdentity = PutModel::verifyUser();
            $modify_bill_data['last_modified_by'] = current($userIdentity)['store_id'];
            $modify_bill_data['last_modified_time'] = date('Y-m-d H:i:s');
            $modify_bill_data['status'] = 2;
            //更改操作
            if (UpdateModel::modifyInvalidBill($condition, $modify_bill_data) === false) {
                throw new \Exception('作废单据失败', 19007);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 2004) {
                $this->addError('invalid', 2004);
                return false;
            } else {
                $this->addError('invalid', 19007);
                return false;
            }
        }
    }

}
