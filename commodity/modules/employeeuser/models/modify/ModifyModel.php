<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\employeeuser\models\modify\db\Update;
use commodity\modules\employeeuser\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';
    const ACTION_STOP = 'action_stop';
    const ACTION_START = 'action_start';

    public $token;
    public $id; //账号id
    public $account_name; //账号名称
    public $passwd; //密码
    public $verify_passwd; //密码
    public $name; //姓名
    public $employee_id; //员工id
    public $status;     //账户状态

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'account_name', 'passwd', 'verify_passwd', 'employee_id', 'status', 'token'
            ],
            self::ACTION_STOP => [
                'id', 'token'
            ],
            self::ACTION_START => [
                'id', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'passwd', 'verify_passwd', 'id', 'token'],
                'required',
                'message' => 2004,
            ],
            [['passwd', 'verify_passwd',], 'string', 'length' => [8, 20], 'tooShort' => 1004, 'tooLong' => 1004, 'message' => 1004],
            [['employee_id'], 'integer', 'min' => 1, 'tooSmall' => 1005, 'message' => 1005],
        ];
    }

    public function actionModify() {

        try {
            $post_data['id'] = $this->id;
            $post_data['passwd'] = $this->passwd;
            $post_data['verify_passwd'] = $this->verify_passwd;
            $post_data['employee_id'] = $this->employee_id;
            $post_data['account_name'] = $this->account_name;
            $post_data['status'] = $this->status;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);

            
            $condition['store_id'] = $modify_data['store_id'];
            $condition['id'] = $this->id;
//            print_r($modify_data);
//            die;
            //更改操作
            if (!Update::modifyEmployeeUser($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } elseif ($ex->getCode() === 1006) {
                $this->addError('modify', 1006);
                return false;
            } elseif ($ex->getCode() === 1007) {
                $this->addError('modify', 1007);
                return false;
            }elseif ($ex->getCode() === 1009) {
                $this->addError('modify', 1009);
                return false;
            }elseif ($ex->getCode() === 1010) {
                $this->addError('modify', 1010);
                return false;
            }elseif ($ex->getCode() === 1016) {
                $this->addError('insert', 1016);
                return false;
            }else {
                $this->addError('modify', 1011);
                return false;
            }
        }
    }

    public function actionStop() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id= $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
            
            $modify_data['status'] = 0;
//            print_r($condition);die;
            //更改操作
            if (Update::modifyAllEmployeeUser($condition, $modify_data) === false) {
                throw new \Exception('账号停止失败', 1012);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('stop', 3006);
                return false;
            } else {
                $this->addError('stop', 1012);
                return false;
            }
        }
    }

    public function actionStart() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_employee_data['status'] =1;
            //更改操作
            if (Update::modifyAllEmployeeUser($condition, $modify_employee_data) === false) {
                throw new \Exception('账号启用失败', 1013);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('start', 3006);
                return false;
            } else {
                $this->addError('start', 1013);
                return false;
            }
        }
    }

}
