<?php

/** 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\models\put;

use Yii;
use common\models\Model as CommonModel;
use admin\modules\adminuser\models\put\db\InsertModel;
use admin\modules\authorization\models\ResourceModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\exceptions;

class PutModel extends CommonModel
{
    const ACTION_USER = 'action_user';

    public $rbac;
    public $account;//帐户 string
    public $name;//账户名 string
    public $passwd;//密码 string
    public $verify_passwd;//确认密码
    public $mobile;//手机号 string
    public $email;//邮箱 string
    public $created_by;//创建人 integer
    public $last_modified_time;//上次修改时间 string
    public $status;//用户状态 integer
    public $role_id;//角色id integer
    
    public function scenarios()
    {
        return [
            self::ACTION_USER => [
                'account', 'name','passwd','verify_passwd', 'mobile','email', 'role_id', 'rbac'
            ],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['name', 'passwd', 'verify_passwd',  'mobile', 'email', 'role_id'],
                'required',
                'message' => 20007,'on'=>self::ACTION_USER
            ],
            ['name', 'string', 'length' => [0, 10],  'tooLong' => 20003, 'message' => 20003, 'on'=>self::ACTION_USER],
            ['passwd', 'string', 'length' => [1, 30],  'tooLong' => 20004, 'message' => 20004, 'on'=>self::ACTION_USER],
            ['email', 'email', 'message' => 20006, 'on'=>self::ACTION_USER],
            ['mobile', 'filter', 'filter' => 'trim', 'on'=>self::ACTION_USER],
            ['mobile','match','pattern'=>'/^[1][345789][0-9]{9}$/', 'message'=>20005, 'on'=>self::ACTION_USER],
            ['status', 'default', 'value' => 1, 'on'=>self::ACTION_USER]
        ];
    }

    /**
     * 创建账号
     * @params
     * @param  $name; //账号名称
     * @param  $passwd; //密码
     * @param  $verify_passwd; //密码
     * @param  $mobile; //手机
     * @param  $email; //邮箱
     * @return bool
     */
    public function actionUser() {
        $result = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $post_data['name'] = $this->name;
            $post_data['passwd'] = $this->passwd;
            $post_data['verify_passwd'] = $this->verify_passwd;
            $post_data['mobile'] = $this->mobile;
            $post_data['email'] = $this->email;
            
            //整理参数
            $add_data = self::prepareData($post_data);
            //执行添加操作
            $user_id = InsertModel::insertAdminUser($add_data);
            if (!$user_id){
                throw new \Exception('插入用户信息失败', 3000);
            }
            $result['account'] = $add_data['account'];
            //保存用户角色关系
            $ResourceModel = new ResourceModel([
                'scenario' => ResourceModel::ASSIGN_ROLE,
                'attributes' => array_merge(
                    ['role_id'=>$this->role_id,'user_id'=>$user_id],
                    ['rbac' => $this->rbac]
                ),
            ]);
            $add_resource = $ResourceModel->assignRole();
            if (!is_array($add_resource)){
                throw new \Exception('保存用户角色关系失败', 20002);
            }
            $transaction->commit();
            $result['account'] = $add_data['account'];
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError($e->getMessage(),$e->getCode());
            return false;
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
        
        //判断密码
        $passwd = array_key_exists('passwd', $data) ? $data['passwd'] : '';
        $verify_passwd = array_key_exists('verify_passwd', $data) ? $data['verify_passwd'] : '';

        if ($passwd !== $verify_passwd) {
            throw new \Exception('密码和确认密码不一致', 1006);
        }

        if (!empty($passwd)){
            $admin_user_data['passwd'] = password_hash($passwd, PASSWORD_DEFAULT);
        }
        $admin_user_data['mobile'] = $data['mobile'];
        $admin_user_data['email'] = $data['email'];

        if ($is_new) {
            $admin_user_data['created_by'] = AccessTokenAuthentication::getUser()['id'] ?? -1;//创建人id
            $admin_user_data['account'] = self::getAdminAccount(8);//账号,自动生成8位随机数
            $admin_user_data['created_time'] = date('Y-m-d H:i:s');//时间赋值
        }

        if (empty($data['name'])) {
            throw new \Exception('帐号添加失败，用户名不能为空', 1008);
        }
        $admin_user_data['name'] = $data['name'];
        $admin_user_data['last_modified_time'] = date('Y-m-d H:i:s');

        return array_filter($admin_user_data);
    }

    /**
     * 生成账号
     *
     * $len  生成账号的长度
     */
    public static function getAdminAccount($len = 9) {

        $condition['account'] = $account = rand(pow(10, $len - 1), pow(10, $len) - 1);

        $info = InsertModel::getField($condition, 'account');
        if ($info) {
            $account = self::getAdminAccount($len);
        }

        return $account;
    }
}

