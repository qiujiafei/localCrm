<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\customerinfomation\models\del\db\Del;
use commodity\modules\customerinfomation\models\put\PutModel;

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
                throw new \Exception('参数错误,客户删除失败', 17057);
            }
        
            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
        
            //删除动作
            if (Del::delCustomerInfomation($condition)===false) {
                throw new \Exception('参数错误,客户删除失败', 17057);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17057) {
                $this->addError('del', 17057);
                return false;
            }  else {
                $this->addError('del', 17058);
                return false;
            }
        }
    }

}
