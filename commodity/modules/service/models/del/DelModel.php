<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\service\models\del\db\Del;
use commodity\modules\service\models\put\PutModel;

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
                throw new \Exception('参数错误,导致服务项目删除失败', 9032);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];
//            print_r($condition);die;
            //删除动作
            if (Del::delService($condition)) {
                return [];
            }
        } catch (\Exception $ex) {

            if ($ex->getCode() === 9032) {
                $this->addError('del', 9032);
                return false;
            } else {
                $this->addError('del', 9031);
                return false;
            }
        }
    }

}
