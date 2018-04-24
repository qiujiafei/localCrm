<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damageddestroy\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\damageddestroy\models\modify\db\Update;
use commodity\modules\damageddestroy\models\put\PutModel;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';
    const ACTION_LEAVE = 'action_leave';
    const ACTION_OPEN = 'action_open';

    public $token;
    public $name; //姓名 
    public $damageddestroy_number; //工号 
    public $damageddestroy_type_name; //工种名 
    public $phone_number; //手机号 
    public $qq_number;  //QQ号  
    public $basic_salary; //基础薪资 
    public $ID_card_image; //身份证照片 
    public $ID_code;  //省份证号
    public $ability; //能力值0-100
    public $attendance_code; //打卡密码
    public $comment; //备注
    public $status;  //状态,默认0,(0:在职, 1:离职)

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'name', 'phone_number', 'damageddestroy_number', 'damageddestroy_type_name', 'qq_number', 'basic_salary', 'ability', 'attendance_code', 'ID_card_image', 'ID_code', 'comment', 'token'
            ],
            self::ACTION_LEAVE => [
                'damageddestroy_number', 'token'
            ],
            self::ACTION_OPEN => [
                'damageddestroy_number', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                ['damageddestroy_number', 'token'],
                'required',
                'message' => 2004,
            ],
            ['name', 'string', 'length' => [1, 10], 'tooShort' => 7001, 'tooLong' => 7002],
            [['phone_number'], 'match', 'pattern' => '/^1[3456789]\d{9}$/ims', 'message' => 7008],
            [['qq_number'], 'match', 'pattern' => '/^\d{5,12}$/isu', 'message' => 7009],
            [['basic_salary'], 'double', 'min' => 0, 'tooSmall' => 7005, 'message' => 7012],
            ['basic_salary', 'string', 'length' => [0, 30], 'tooLong' => 7006],
            [['ability'], 'integer', 'min' => 0, 'max' => 100, 'tooSmall' => 7003, 'tooBig' => 7004],
            [['ID_card_image'], 'image', 'notImage' => 7010],
            [['ID_code'], 'match', 'pattern' => '/^([\d]{17}[xX\d]|[\d]{15})$/isu', 'message' => 7011],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 7007],
        ];
    }

    public function actionModify() {

        try {

            $post_data['damageddestroy_number'] = $this->damageddestroy_number;
            $post_data['name'] = $this->name;
            $post_data['damageddestroy_type_name'] = $this->damageddestroy_type_name;
            $post_data['phone_number'] = $this->phone_number;
            $post_data['qq_number'] = $this->qq_number;
            $post_data['basic_salary'] = $this->basic_salary;
            $post_data['ability'] = $this->ability;
            $post_data['ID_code'] = $this->ID_code;
            $post_data['ID_card_image'] = $this->ID_card_image;
            $post_data['attendance_code'] = $this->attendance_code;

            $post_data['comment'] = $this->comment;

            //整理参数
            $modify_damageddestroy_data = PutModel::prepareData($post_data, false);



            $condition['store_id'] = $modify_damageddestroy_data['store_id'];
            $condition['damageddestroy_number'] = $this->damageddestroy_number;
            unset($modify_damageddestroy_data['damageddestroy_number']);
//            print_r($condition);
//            die;
            //更改操作
            if (!Update::modifyEmployee($condition, $modify_damageddestroy_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } else {
                $this->addError('modify', 7014);
                return false;
            }
        }
    }

    public function actionLeave() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $damageddestroy_number = $this->damageddestroy_number;
            if (!is_array($damageddestroy_number)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            $condition['damageddestroy_number'] = $damageddestroy_number;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_damageddestroy_data['status'] = 1;
            //更改操作
            if (Update::modifyAllEmployee($condition, $modify_damageddestroy_data) === false) {
                throw new \Exception('员工离职失败', 7018);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('leave', 3006);
                return false;
            } else {
                $this->addError('leave', 7018);
                return false;
            }
        }
    }

    public function actionOpen() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $damageddestroy_number = $this->damageddestroy_number;
            if (!is_array($damageddestroy_number)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            $condition['damageddestroy_number'] = $damageddestroy_number;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_damageddestroy_data['status'] = 0;
            //更改操作
            if (Update::modifyAllEmployee($condition, $modify_damageddestroy_data) === false) {
                throw new \Exception('员工启用失败', 7019);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('open', 3006);
                return false;
            } else {
                $this->addError('open', 7019);
                return false;
            }
        }
    }

}
