<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\models\del;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\commodityUnit\models\del\db\Del;
use commodity\modules\commodityUnit\models\del\db\Veyrify;
use commodity\modules\commodityUnit\models\put\PutModel;

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

    /**
     * 删除单位
     */
    public function actionDel() {
        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();

            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致删除失败', 3003);
            }

            $condition['unit_id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            //判断单位下有没有商品
            if (!Veyrify::verifyClassCommodity($condition)) {
                throw new \Exception('单位有对应的商品，不能做删除操作', 3005);
            }
            unset($condition['unit_id']);
            $condition['id'] = $id;
            //删除动作
            if (Del::delCommodityUnit($condition)) {
                return [];
            }
        } catch (\Exception $ex) {

            if ($ex->getCode() === 3003) {
                $this->addError('del', 3003);
                return false;
            } elseif ($ex->getCode() === 3005) {
                $this->addError('del', 3005);
                return false;
            } else {
                $this->addError('del', 3004);
                return false;
            }
        }
    }

}
