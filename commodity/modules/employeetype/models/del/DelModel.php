<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeetype\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\employeetype\models\del\db\Del;
use commodity\modules\employeetype\models\del\db\Veyrify;
use commodity\modules\employeetype\models\put\PutModel;

class DelModel extends CommonModel {

    const ACTION_DEL = 'action_del';

    public $id;
    public $token;

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
            $store_id = current($userIdentity)['store_id'];
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致工种删除失败', 6006);
            }
            
            $veyrify_condition[0] = 'and';
            $veyrify_condition[1] = ['in','employee_type_id',$id];
            $veyrify_condition[2] = 'store_id=' . $store_id;
            $veyrify_condition[3] = ['!=','status',2];//
           
            //判断工种有没有对应员工
            if (!Veyrify::veyrifyTypeEmployee($veyrify_condition)) {
                throw new \Exception('工种有对应的员工，不能做删除操作', 6007);
            }
            
            $condition['store_id'] =$store_id;
            $condition['id'] = $id;

            //删除动作
            if (Del::delEmployeeType($condition)) {
                return [];
            }
            
        } catch (\Exception $ex) {

            if ($ex->getCode() === 6006) {
                $this->addError('del', 6006);
                return false;
            } elseif ($ex->getCode() === 6007) {
                $this->addError('del', 6007);
                return false;
            } else {
                $this->addError('del', 6008);
                return false;
            }
        }
    }

}
