<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace admin\modules\testAccount\models\modify;

use common\models\Model;
use admin\modules\testAccount\models\modify\db\Update;
use admin\modules\testAccount\models\put\PutModel;
use common\exceptions;

class ModifyModel extends Model
{
    const ACTION_MODIFY = 'action_modify';
    const ACTION_FORBID = 'action_forbid';
    const ACTION_USING = 'action_using';

    public $id;
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
            self::ACTION_UPDATE => [
                'id', 'account', 'account_name', 'passwd', 'verify_passwd', 'mobile', 'email',
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
     * 门店修改
     */
    public function actionModity() {

        try {
            $post_data['id'] = $this->id;
            $post_data['passwd'] = $this->passwd;
            $post_data['verify_passwd'] = $this->verify_passwd;
            $post_data['account_name'] = $this->account_name;
            $post_data['mobile'] = $this->mobile;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            $condition['id'] = $this->id;
            //更改操作
            if (!Update::modifyStore($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            }else {
                $this->addError('modify', 1011);
                return false;
            }
        }
    }
    
    /**
     * 门店禁止
     */
     public function actionForbid() {

        try {
            $post_data['id'] = $this->id;
            $post_data['passwd'] = $this->passwd;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            $condition['id'] = $this->id;
            //更改操作
            if (!Update::modifyAllStore($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            }else {
                $this->addError('modify', 1011);
                return false;
            }
        }
    }
    
    
    /**
     * 门店启用
     */
      public function actionUsing() {

        try {
            $post_data['id'] = $this->id;
            $post_data['passwd'] = $this->passwd;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            $condition['id'] = $this->id;
            //更改操作
            if (!Update::modifyAllStore($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            }else {
                $this->addError('modify', 1011);
                return false;
            }
        }
    }
}
