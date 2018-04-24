<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\put;

use common\models\Model as CommonModel;
use commodity\modules\employeeuser\models\put\db\Insert;
use commodity\modules\employee\models\put\db\Insert as Employee_Insert;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $account_name; //账号名称
    public $passwd; //密码
    public $verify_passwd; //密码
    public $name; //姓名
    public $employee_id; //员工id
    public $status;     //账户状态

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'account_name', 'passwd', 'verify_passwd', 'employee_id', 'status', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'passwd', 'verify_passwd', 'employee_id', 'token'],
                'required',
                'message' => 2004,
            ],
            [['passwd', 'verify_passwd',], 'string', 'length' => [8, 20], 'tooShort' => 1004, 'tooLong' => 1004, 'message' => 1004],
            [['employee_id'], 'integer', 'min' => 1, 'tooSmall' => 1005, 'message' => 1005],
            ['status', 'default', 'value' => 1],
        ];
    }

    /**
     * 创建员工账号
     * @params 
     *          public $account_name; //账号名称
     *          public $passwd; //密码
     *          public $verify_passwd; //密码
     *          public $name; //姓名
     *          public $employee_id; //员工
     * 
     * @return bool 
     */
    public function actionInsert() {
        try {

            $post_data['passwd'] = $this->passwd;
            $post_data['verify_passwd'] = $this->verify_passwd;
            $post_data['employee_id'] = $this->employee_id;
            $post_data['account_name'] = $this->account_name;
            $post_data['status'] = $this->status;

            //整理参数
            $add_data = self::prepareData($post_data);
//
//            print_r($add_data);
//            die;
            //添加操作
            Insert::insertEmployeeUser($add_data);
            $account['account'] = $add_data['account'];
            return $account;
        } catch (\Exception $ex) {
            if ($ex->getCode() === 1006) {
                $this->addError('insert', 1006);
                return false;
            } elseif ($ex->getCode() === 1007) {
                $this->addError('insert', 1007);
                return false;
            } elseif ($ex->getCode() === 1016) {
                $this->addError('insert', 1016);
                return false;
            } else {
                $this->addError('insert', 1008);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();
        $employee_user_data['store_id'] = $condition['store_id'] = current($userIdentity)['store_id'];
        //判断密码
        $passwd = array_key_exists('passwd', $data) ? $data['passwd'] : '';
        $verify_passwd = array_key_exists('verify_passwd', $data) ? $data['verify_passwd'] : '';

        if ($passwd !== $verify_passwd) {
            throw new \Exception('密码和确认密码不一致', 1006);
        }

        $employee_user_data['passwd'] = password_hash($passwd, PASSWORD_DEFAULT);

        if (!$switch) {
            $condition['id'] = $data['id'];
            $acount_info = Insert::getField($condition, 'passwd');
            if (!$acount_info) {
                throw new \Exception('该用户不存在', 1009);
                return false;
            }
//            if (!Yii::$app->security->validatePassword($passwd, $acount_info['passwd'])) {
//                throw new \Exception('密码输入有误', 1010);
//                return false;
//            }
        }


        $employee_user_data['account_name'] = array_key_exists('account_name', $data) ? $data['account_name'] : '';

        $employee_user_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';

        $employee_id = array_key_exists('employee_id', $data) ? $data['employee_id'] : '';

        if ($employee_id) {
            $condition['id'] = $employee_id;
            $employee_info = Employee_Insert::getField($condition, 'name');
            if (!$employee_info) {
                throw new \Exception('员工不存在', 1007);
            }

            $user_condition['employee_id'] = $employee_id;
            $user_info = Insert::getField($user_condition, 'employee_id,id');
            if ($user_info) {
                if ($switch || (!$switch && $user_info->id != $data['id'])) {
                    throw new \Exception('该员工账号已存在', 1016);
                }
            }

            $employee_user_data['employee_id'] = $employee_id;
            $employee_user_data['name'] = $employee_info['name'];
        }

        //账号,自动生成9位随机数
        if ($switch) {
            $employee_user_data['account'] = self::getEmployeeAccount(9);
        }

//        $employee_user_data['last_modified_by'] = current($userIdentity)['id'];
        $employee_user_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $employee_user_data['created_time'] = date('Y-m-d H:i:s');
            $employee_user_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($employee_user_data, function ($v) {
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

    /**
     * 生成账号
     * 
     * $len  生成账号的长度
     */
    public static function getEmployeeAccount($len = 9) {
        
        $condition['account'] = $account = rand(pow(10, $len - 1), pow(10, $len) - 1);
        
        $info = Insert::getField($condition, 'account');
        if ($info) {
            $account = self::getEmployeeAccount($len);
        }
        
        return $account;
    }

}
