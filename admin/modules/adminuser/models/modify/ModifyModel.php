<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */
namespace admin\modules\adminuser\models\modify;

use common\models\Model;
use admin\modules\adminuser\models\modify\db\Update;
use admin\modules\adminuser\models\put\PutModel;
use admin\modules\authorization\models\ResourceModel;
use common\exceptions;
use Yii;

class ModifyModel extends Model
{
    const ACTION_UPDATE = 'action_update';
    const DEFAULT_COMMODITY_PROPERTY = '其他';

    public $rbac;
    public $id;
    public $account;//帐户 string
    public $name;//账户名 string
    public $passwd;//密码 string
    public $verify_passwd;//确认密码
    public $mobile;//手机号 string
    public $email;//邮箱 string
    public $role_id;//角色id integer
    public $last_modified_time;//上次修改时间 string
    public $status;//用户状态 integer
    
    public function scenarios()
    {
        return [
            self::ACTION_UPDATE => [
                'id', 'name', 'passwd', 'verify_passwd', 'mobile', 'email', 'role_id', 'rbac'
            ],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['id', 'name', 'mobile', 'email' ,'role_id'],
                'required',
                'message' => 20007,
            ],
            [['id','role_id'],'integer', 'message'=> 3006],
            ['name', 'string', 'length' => [0, 10],  'tooLong' => 20003, 'message' => 20003],
            ['passwd', 'string', 'length' => [1, 30],  'tooLong' => 20004, 'message' => 20004],
            ['verify_passwd', 'string', 'length' => [1, 30],  'tooLong' => 20004, 'message' => 20004],
            ['email', 'email', 'message' => 20006],
            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile','match','pattern'=>'/^[1][345789][0-9]{9}$/', 'message'=>20005],
            ['status', 'default', 'value' => 1]
        ];
    }

    public function actionUpdate() {

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $post_data['id'] = $this->id;
            $post_data['passwd'] = $this->passwd;
            $post_data['verify_passwd'] = $this->verify_passwd;
            $post_data['name'] = $this->name;
            $post_data['mobile'] = $this->mobile;
            $post_data['email'] = $this->email;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            $condition['id'] = $this->id;
            //执行用户更改操作
            if (!Update::modifyAdminUser($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3003);
            }
            //同步更新用户角色关系表
            $ResourceModel = new ResourceModel([
                'scenario' => ResourceModel::ASSIGN_ROLE,
                'attributes' => array_merge(
                    ['role_id'=>$this->role_id,'user_id'=>$this->id],
                    ['rbac' => $this->rbac]
                ),
            ]);
            $modify_resource = $ResourceModel->assignRole();
            if (!is_array($modify_resource)){
                throw new \Exception('修改用户角色关系失败', 20002);
            }

            $transaction->commit();
            return [];
        } catch (\Exception $e) {
            if ($e->getCode() == 0) {
                $this->addError('修改用户失败',20000);
            }
            $this->addError($e->getMessage(),$e->getCode());
            $transaction->rollBack();
            return false;
        }
    }
}
