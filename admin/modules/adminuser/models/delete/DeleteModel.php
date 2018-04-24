<?php

/**
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */
namespace admin\modules\adminuser\models\delete;

use Yii;
use common\models\Model as BaseModel;
use admin\modules\adminuser\models\delete\db\Del;
use admin\modules\authorization\models\RoleModel;

class DeleteModel extends BaseModel
{
    const ACTION_ONE = 'action_one';

    public $rbac;
    public $id;
    
    public function scenarios()
    {
        return [
            self::ACTION_ONE => ['id','rbac'],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['id'],
                'required',
                'message' => 1,
            ],
        ];
    }
    
    public function actionOne()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //判断是否存在该用户
            $user = Del::isExists($this->id);
            if (empty($user)){
                throw new \Exception('该用户不存在',1009);
            }else{
                if(isset($user['type']) && $user['type'] == 2){
                    throw new \Exception('该账户无法操作',20008);
                }
            }
            //执行删除
            if (!Del::delAdminUser(['id'=>$this->id])) {
                throw new \Exception('账号删除失败', 1015);
            }
            $RoleModel = new RoleModel([
                'scenario' => RoleModel::REVOKE,
                'attributes' => array_merge(
                    ['user_id'=>$this->id],
                    ['rbac' => $this->rbac]
                ),
            ]);
            $RoleModel->revoke();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
        return [];
    }
}
