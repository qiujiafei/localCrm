<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\models\put;

use Yii;
use common\models\Model as CommonModel;
use admin\modules\testAccount\models\put\db\Insert;
use common\exceptions;

class PutModel extends CommonModel
{
    const ACTION_INSERT = 'action_insert';
   
    public $account;//帐户 string
    public $account_name;//账户名 string
    public $passwd;//密码 string
    public $verify_passwd;//确认密码
    public $mobile;//手机号 string
    public $email;//邮箱 string
    public $created_by;//创建人 integer
    public $last_modified_time;//上次修改时间 string
    public $status;//用户状态 integer
    
    public function scenarios()
    {
        return [
            self::ACTION_INSERT => [
                'account', 'account_name','passwd','verify_passwd', 'mobile','email','created_by',
            ],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['account_name', 'passwd', 'verify_passwd',  'mobile', 'email'],
                'required',
                'message' => 2004,
            ],
            ['account_name', 'string', 'length' => [0, 10],  'tooLong' => 1004, 'message' => 1004],
            [['passwd','email'], 'string', 'length' => [0, 30],  'tooLong' => 1004, 'message' => 1004],
            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile','match','pattern'=>'/^[1][34578][0-9]{9}$/'],
            ['status', 'default', 'value' => 1]
        ];
    }

    /**
     * 创建账号
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

            $post_data['account_name'] = $this->account_name;
            $post_data['passwd'] = $this->passwd;
            $post_data['verify_passwd'] = $this->verify_passwd;
            $post_data['mobile'] = $this->mobile;
            $post_data['email'] = $this->email;
            
            //整理参数
            $add_data = self::prepareData($post_data);
            //添加操作
            Insert::insertAdminUser($add_data);
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

    /**
     * prepareData
     * @param array $data
     * @param bool $is_new 判断是否为新建
     * @return array|bool
     * @throws \Exception
     */
    public static function prepareData(array $data, bool $is_new = true) {

        //判断user是否存在
        //$userIdentity = self::verifyUser();
        //判断密码
        $passwd = array_key_exists('passwd', $data) ? $data['passwd'] : '';
        $verify_passwd = array_key_exists('verify_passwd', $data) ? $data['verify_passwd'] : '';

        if ($passwd !== $verify_passwd) {
            throw new \Exception('密码和确认密码不一致', 1006);
        }

        $admin_user_data['passwd'] = password_hash($passwd, PASSWORD_DEFAULT);

        if (!$is_new) {
            $condition['id'] = $data['id'];
            $acount_info = Insert::getField($condition, 'id');
            if (!$acount_info) {
                throw new \Exception('该用户不存在', 1009);
                return false;
            }
        }else{
            $admin_user_data['account'] = self::getAdminAccount(9);//账号,自动生成9位随机数
            $admin_user_data['created_time'] = date('Y-m-d H:i:s');//时间赋值
        }

        $admin_user_data['account_name'] = array_key_exists('account_name', $data) ? $data['account_name'] : '';
        
        
        
        $admin_user_data['last_modified_time'] = date('Y-m-d H:i:s');
        
        //$admin_user_data['created_by'] = current($userIdentity)['id'];//创建人赋值

        return array_filter($admin_user_data);
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
        //return Yii::$app->user->getIdentity()::$user ?? null;
    }

    /**
     * 生成账号
     *
     * $len  生成账号的长度
     */
    public static function getAdminAccount($len = 9) {

        $condition['account'] = $account = rand(pow(10, $len - 1), pow(10, $len) - 1);

        $info = Insert::getField($condition, 'account');
        if ($info) {
            $account = self::getAdminAccount($len);
        }

        return $account;
    }
}

