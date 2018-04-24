<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\serviceaddition\models\del\db\Del;
use commodity\modules\serviceaddition\models\put\PutModel;

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

            if (!is_array($id)) {
                throw new \Exception('参数异常,附加项目删除失败', 9008);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            //删除动作
            if (Del::delServiceAddition($condition)) {
                return [];
            }
        } catch (\Exception $ex) {

            if ($ex->getCode() === 9008) {
                $this->addError('del', 9008);
                return false;
            } else {
                $this->addError('del', 9009);
                return false;
            }
        }
    }

}
