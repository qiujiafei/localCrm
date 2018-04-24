<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\employee\models\del\db\Del;
use commodity\modules\employee\models\put\PutModel;
use commodity\modules\employee\models\modify\ModifyModel;
use commodity\modules\employee\models\modify\db\Update;

class DelModel extends CommonModel {

    const ACTION_DEL = 'action_del';

    public $token;
    public $employee_number; //工号 string

    public function scenarios() {
        return [
            self::ACTION_DEL => [
                'employee_number', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['employee_number', 'token'],
                'required',
                'message' => 2004,
            ],
        ];
    }

    public function actionDel() {

        try {


            //判断user是否存在
            $userIdentity = PutModel::verifyUser();

            $employee_number = $this->employee_number;
            if (!is_array($employee_number)) {
                throw new \Exception('参数错误,导致员工删除失败', 7015);
            }

            $condition['employee_number'] = $employee_number;
            $condition['store_id'] = current($userIdentity)['store_id'];

            ModifyModel::verifyEmployeeUser($condition);


            $modify_employee_data['status'] = 2;
            //删除动作
            if (Update::modifyAllEmployee($condition, $modify_employee_data) === false) {
                throw new \Exception('参数错误,导致员工删除失败', 7015);
                return false;
            }

            return [];
        } catch (\Exception $ex) {

            if ($ex->getCode() === 7015) {
                $this->addError('del', 7015);
                return false;
            } elseif ($ex->getCode() === 3006) {
                $this->addError('del', 7015);
                return false;
            } elseif ($ex->getCode() === 7022) {
                $this->addError('del', 7022);
                return false;
            } else {
                $this->addError('del', 7016);
                return false;
            }
        }
    }

}
