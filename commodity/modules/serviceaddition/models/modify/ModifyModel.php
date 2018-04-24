<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\serviceaddition\models\modify\db\Update;
use commodity\modules\serviceaddition\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';

    public $id; 
    public $addition_name; //附加项目名称
    public $price; //售价
    public $status; //预留状态默认1
    public $token;

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'addition_name', 'price','status', 'token'
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['id','token'],
                'required',
                'message' => 2004,
            ],
            [
                ['addition_name'],
                'string',
                'length' => [1, 30],
                'tooShort' => 9001,
                'tooLong' => 9002,
                 'message' => 9039
            ],
            [['price'], 'double', 'min' => 0, 'tooSmall' => 9003, 'message' => 9004],
            ['price', 'string', 'length' => [0, 30], 'tooLong' => 9005],
        ];
    }

    public function actionModify() {
        
        try {
            
            $post_data['id'] = $this->id;
            $post_data['addition_name'] = $this->addition_name;
            $post_data['price'] = $this->price;
            $post_data['status'] = $this->status;
            
            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
//            print_r($modify_data);die;
            $condition['id'] = $this->id;
            $condition['store_id'] = $modify_data['store_id'];

            //更改操作
            if (!Update::modifyServiceAddition($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            }  elseif ($ex->getCode() === 9006) {
                $this->addError('modify', 9006);
                return false;
            } else {
                $this->addError('modify', 9007);
                return false;
            }
        }
    }

}
