<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\models\put;

use common\models\Model as CommonModel;
use commodity\modules\employee\models\put\db\Insert;
use common\components\upload\services\GeneralInvokableFactory;
use commodity\modules\employeetype\models\put\db\Insert as EmployeeType;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
    public $name; //姓名 string
    public $employee_number; //工号 string
    public $employee_type_name; //工种名 string
    public $phone_number; //手机号 string
    public $qq_number;  //QQ号  
    public $basic_salary; //基础薪资 string
    public $ID_card_image; //身份证照片 string
    public $ID_code;  //省份证号
    public $ability; //能力值0-100
    public $attendance_code; //打卡密码
    public $comment; //备注
    public $status;  //状态,默认0,(0:在职, 1:离职)
    public $suffix = ['gif', 'jpeg', 'png', 'bmp', 'jpg']; //图片格式

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'name', 'phone_number', 'employee_type_name', 'qq_number', 'basic_salary', 'ID_card_image', 'ability', 'attendance_code', 'status', 'ID_code', 'comment', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'name', 'phone_number', 'employee_type_name', 'token'],
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
            ['status', 'in', 'range' => [0, 1], 'message' => 7013],
            ['status', 'default', 'value' => 0],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 7007],
        ];
    }

    /**
     * 添加员工模块接口
     * @params 
     *           $name; //姓名 string
     *           $employee_number; //工号 string
     *           $employee_type_name; //工种名 string
     *           $phone_number; //手机号 string
     *           $qq_number;  //QQ号  
     *           $basic_salary; //基础薪资 string
     *           $ID_card_image; //身份证照片 string
     *           $ID_code;  //省份证号
     *           $ability; //能力值0-100
     *           $attendance_code; //打卡密码
     *           $comment; //备注
     *           $status;  //状态,默认0,(0:在职, 1:离职)
     * 
     * @return bool 
     */
    public function actionInsert() {
        try {

            $post_data['name'] = $this->name;
            $post_data['employee_type_name'] = $this->employee_type_name;
            $post_data['phone_number'] = $this->phone_number;
            $post_data['qq_number'] = $this->qq_number;
            $post_data['basic_salary'] = $this->basic_salary;
            $post_data['ability'] = $this->ability;
            $post_data['ID_code'] = $this->ID_code;

            if ($this->ID_card_image) {
                $post_data['ID_card_image'] = Yii::$app->params['OSS_PostHost'] . '/' . $this->ID_card_image;
            }

            $post_data['attendance_code'] = $this->attendance_code;
            $post_data['status'] = $this->status;
            $post_data['comment'] = $this->comment;

            //整理参数
            $add_employee_data = self::prepareData($post_data);

//            print_r($add_employee_data);die;
            //添加操作
            Insert::insertEmployee($add_employee_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 7020) {
                $this->addError('insert', 7020);
                return false;
            } elseif ($ex->getCode() === 7021) {
                $this->addError('insert', 7021);
                return false;
            } else {
                $this->addError('insert', 7000);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        $employee_data['store_id'] = $employee_type_condition['store_id'] = $condition['store_id'] = current($userIdentity)['store_id'];

        //生成employee参数

        $employee_data['name'] = array_key_exists('name', $data) ? $data['name'] : '';

        $employee_data['employee_type_name'] = $employee_type_name = array_key_exists('employee_type_name', $data) ? $data['employee_type_name'] : '';
        if ($employee_type_name) {
            $employee_type_condition['name'] = $employee_type_name;
            $employee_type = EmployeeType::getField($employee_type_condition, 'id');
            if (!$employee_type) {
                throw new \Exception('工种参数有误', 7021);
                return false;
            }
            $employee_data['employee_type_id']=$employee_type->id;
        }

        $employee_data['phone_number'] = array_key_exists('phone_number', $data) ? $data['phone_number'] : '';

        $employee_data['qq_number'] = array_key_exists('qq_number', $data) ? $data['qq_number'] : '';

        $employee_data['basic_salary'] = array_key_exists('basic_salary', $data) ? $data['basic_salary'] : '';

        //图片大小小于2MB
        $employee_data['ID_card_image'] = array_key_exists('ID_card_image', $data) ? $data['ID_card_image'] : '';

        $employee_data['ID_code'] = array_key_exists('ID_code', $data) ? $data['ID_code'] : '';

        $employee_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';

        $employee_data['attendance_code'] = array_key_exists('attendance_code', $data) ? $data['attendance_code'] : '';

        $employee_data['ability'] = array_key_exists('comment', $data) ? $data['ability'] : '';

        $employee_data['status'] = array_key_exists('status', $data) ? $data['status'] : 0;

        //工号,自动生成6位随机数
        if ($switch) {
            $employee_data['employee_number'] = self::getEmployeeNumber(6, $condition);
        }

        $employee_data['last_modified_by'] = current($userIdentity)['id'];
        $employee_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $employee_data['created_time'] = date('Y-m-d H:i:s');
            $employee_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($employee_data, function ($v) {
                        if ($v === '' || $v === NULL) {   //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
                            return false;
                        }
                        return true;
                    });
    }

    public static function verifyUser() {
        if (!$userIdentity = self::getUser()) {
            throw new \Exception(sprintf(
                    "Can not found user identity in %s.", __METHOD__
            ));
        }
        return $userIdentity;
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

    /**
     * 生成工号
     * 
     * $len  生成员工工号的长度
     */
    public static function getEmployeeNumber($len = 6, $condition) {

        $condition['employee_number'] = $employee_number = rand(pow(10, $len - 1), pow(10, $len) - 1);

        $info = Insert::getField($condition, 'employee_number');
        if ($info) {
            $employee_number = self::getEmployeeNumber($len, $condition);
        }

        return (string) $employee_number;
    }

}
