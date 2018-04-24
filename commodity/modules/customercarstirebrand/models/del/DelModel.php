<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\customercarstirebrand\models\del\db\Del;
use commodity\modules\customercarstirebrand\models\put\PutModel;

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
                throw new \Exception('参数错误,导致轮胎品牌删除失败', 15005);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
           
            //删除动作
            if (!Del::delCustomerCarsTireBrand($condition)) {
                throw new \Exception('参数错误,导致轮胎品牌删除失败', 15005);
            }

            return [];
        } catch (\Exception $ex) {

            if ($ex->getCode() === 15005) {
                $this->addError('del', 15005);
                return false;
            }  else {
                $this->addError('del', 15006);
                return false;
            }
        }
    }

}
