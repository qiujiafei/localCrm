<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\service\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\service\models\modify\db\Update;
use commodity\modules\service\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';
    const ACTION_STOP = 'action_stop';
    const ACTION_OPEN = 'action_open';

    public $token; //服务项目名称
    public $id;
    public $service_name; //服务项目名称
    public $specification; //规格
    public $service_code; //编码
    public $price;     //售价
    public $type;     //类型默认0 (0:非自助, 1:自助)
    public $service_claasification_id;     //服务项目分类ID
    public $comment; //备注

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'service_name', 'specification', 'price', 'type', 'service_claasification_id', 'service_code', 'comment', 'token'
            ],
            self::ACTION_STOP => [
                'id', 'token'
            ],
            self::ACTION_OPEN => [
                'id', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                [ 'id', 'token'],
                'required',
                'message' => 2004,
            ],
            [['service_name', 'old_service_name'], 'string', 'length' => [1, 30], 'tooShort' => 9017, 'tooLong' => 9018],
            ['specification', 'string', 'length' => [1, 30], 'tooShort' => 9026, 'tooLong' => 9027],
            [['price'], 'double', 'min' => 0, 'tooSmall' => 9003, 'message' => 9004],
            ['price', 'string', 'length' => [0, 30], 'tooLong' => 9005],
            ['service_claasification_id', 'integer', 'min' => 1, 'tooSmall' => 9019, 'message' => 9019],
            ['service_code', 'string', 'length' => [8, 30], 'tooShort' => 9024, 'tooLong' => 9025],
            [['type'], 'in', 'range' => [0, 1], 'message' => 9020],
            ['comment', 'string', 'length' => [0, 100], 'tooLong' => 9021],
        ];
    }

    public function actionModify() {

        try {

            $post_data['id'] = $this->id;
            $post_data['service_name'] = $this->service_name;
            $post_data['specification'] = $this->specification;
            $post_data['service_code'] = $this->service_code;
            $post_data['price'] = $this->price;
            $post_data['type'] = $this->type;
            $post_data['service_claasification_id'] = $this->service_claasification_id;
            $post_data['comment'] = $this->comment;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            $modify_data['type'] = $this->type;
//            print_r($modify_data);
//            die;
            $condition['id'] = $this->id;
            $condition['store_id'] = $modify_data['store_id'];

            //更改操作
            if (!Update::modifyService($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } elseif ($ex->getCode() === 9028) {
                $this->addError('modify', 9028);
                return false;
            } elseif ($ex->getCode() === 9022) {
                $this->addError('modify', 9022);
                return false;
            } elseif ($ex->getCode() === 9023) {
                $this->addError('modify', 9023);
                return false;
            } else {
                $this->addError('modify', 9030);
                return false;
            }
        }
    }

    public function actionStop() {

        try {
            $userIdentity = PutModel::verifyUser();

            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致服务项目停止失败', 9038);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_data['status'] = 0;
            //更改操作
            if (Update::modifyAllService($condition, $modify_data) === false) {
                throw new \Exception('服务项目停用失败', 9037);
                return false;
            }
            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 9038) {
                $this->addError('stop', 9038);
                return false;
            } else {
                $this->addError('stop', 9037);
                return false;
            }
        }
    }

    public function actionOpen() {

        try {
            $userIdentity = PutModel::verifyUser();

            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致服务项目启用失败', 9040);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_data['status'] = 1;
            //更改操作

            if (Update::modifyAllService($condition, $modify_data) === false) {
                throw new \Exception('服务项目启用失败', 9041);
                return false;
            }
            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 9040) {
                $this->addError('open', 9040);
                return false;
            } else {
                $this->addError('open', 9041);
                return false;
            }
        }
    }

}
