<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\customercarstirebrand\models\modify\db\Update;
use commodity\modules\customercarstirebrand\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';

    public $token;
    public $id; 
    public $brand_name;

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'brand_name', 'token'
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
            ['brand_name', 'string', 'length' => [1, 30], 'tooShort' => 15000, 'tooLong' => 15001],
        ];
    }

    public function actionModify() {

        try {

            $post_data['id'] = $this->id;
            $post_data['brand_name'] = $this->brand_name;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            
            unset($modify_data['id']);
            
//            print_r($modify_data);die;
            
            $condition['store_id'] = $modify_data['store_id'];
            $condition['id'] = $this->id;
            //更改操作
            if (!Update::modifyCustomerCarsTireBrand($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } elseif ($ex->getCode() === 15002) {
                $this->addError('modify', 15002);
                return false;
            } else {
                $this->addError('modify', 15004);
                return false;
            }
        }
    }

  
}
