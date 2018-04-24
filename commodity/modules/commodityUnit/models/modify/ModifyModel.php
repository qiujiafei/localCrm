<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\commodityUnit\models\modify\db\Update;
use commodity\modules\commodityUnit\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';

    public $token;
    public $id;
    public $unit_name;

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'unit_name', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['id', 'token'],
                'required',
                'message' => 2004
            ],
            ['unit_name', 'string', 'length' => [0, 10], 'tooLong' => 3002],
        ];
    }

    /**
     * 更改单位信息
     */
    public function actionModify() {

        try {
            $post_data['unit_name'] = $this->unit_name;
            $post_data['id'] = $this->id;

            //整理参数
            $modify_unit_data = PutModel::prepareData($post_data, false);
            $condition['id'] = $this->id;
            $condition['store_id'] = $modify_unit_data['store_id'];

            //更改操作
            if (!Update::modifyUnit($condition, $modify_unit_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {

            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } elseif ($ex->getCode() === 3001) {
                $this->addError('insert', 3001);
                return false;
            } else {
                $this->addError('modify', 3007);
                return false;
            }
        }
    }

}
