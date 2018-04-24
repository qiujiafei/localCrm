<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\employeeuser\models\del\db\Del;
use commodity\modules\employeeuser\models\put\PutModel;

class DelModel extends CommonModel {
    
    const ACTION_DEL = 'action_del';
    
    public $token;
    public $id;

    public function scenarios() {
        return [
            self::ACTION_DEL => [
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

    public function actionDel() {

        try {

           
            //判断user是否存在
            $userIdentity = PutModel::verifyUser();

            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致账号删除失败', 1014);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
           
            //删除动作
            if (Del::delEmployee($condition)) {
                return [];
            }

            
        } catch (\Exception $ex) {

            if ($ex->getCode() === 1014) {
                $this->addError('del', 1014);
                return false;
            }  else {
                $this->addError('del', 1015);
                return false;
            }
        }
    }

}
