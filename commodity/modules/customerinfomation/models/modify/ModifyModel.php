<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\modify;

use Yii;
use common\models\Model as CommonModel;
use commodity\modules\customerinfomation\models\modify\db\Update;
use commodity\modules\customerinfomation\models\put\PutModel;
use commodity\modules\customerinfomation\models\put\db\Insert;
use commodity\modules\customerinfomation\models\put\db\InsertMember;

class ModifyModel extends CommonModel {

    const ACTION_MODIFY = 'action_modify';
    const ACTION_STOP = 'action_stop';
    const ACTION_OPEN = 'action_open';
    const ACTION_ISMEMBER = 'action_ismember';
    const ACTION_NOMEMBER = 'action_nomember';

    public $token;
    public $id;
    public $customer_name; //客户姓名 string
    public $cellphone_number; //手机号码
    public $gender; //性别(0:女 1:男 2:其他)
    public $birthday;  //生日
    public $ID_number; //身份证
    public $address; //地址
    public $customer_origination; //客户来源
    public $license_image_name; //驾驶证图片ID
    public $company; //单位名称
    public $is_member; //是否会员 默认0(0:否 1:是)'
    public $comment; //备注
    public $status;  //状态 默认1(0:停用 1:启用)
    public $consume_count;  //消费次数
    public $total_consume_price; //累计消费
    public $car_info; //汽车信息
    public $card_number; //会员卡号
    public $type; //会员卡种
    public $price; //办卡金额
    public $customer_comment; //备注
    public $suffix = ['gif', 'jpeg', 'png', 'bmp', 'jpg']; //图片格式

    public function scenarios() {
        return [
            self::ACTION_MODIFY => [
                'id', 'customer_name', 'cellphone_number', 'gender', 'birthday', 'license_image_name', 'ID_number', 'address', 'customer_origination', 'is_member', 'license_image_id', 'company', 'status', 'comment', 'car_info', 'token'
            ],
            self::ACTION_STOP => [
                'id', 'token'
            ],
            self::ACTION_OPEN => [
                'id', 'token'
            ],
            self::ACTION_ISMEMBER => [
                'id', 'card_number', 'type', 'price', 'customer_comment', 'token'
            ],
            self::ACTION_NOMEMBER => [
                'id', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'id', 'card_number', 'type', 'price', 'token'],
                'required',
                'message' => 2004,
            ],
            ['customer_name', 'string', 'length' => [1, 30], 'tooShort' => 17000, 'tooLong' => 17000], //姓名
            [['cellphone_number'], 'match', 'pattern' => '/^1[3456789]\d{9}$/ims', 'message' => 17001], //手机号
            ['gender', 'in', 'range' => [0, 1, 2], 'message' => 17002], //性别
            ['gender', 'default', 'value' => 0],
            [['ID_number'], 'match', 'pattern' => '/^([\d]{17}[xX\d]|[\d]{15})$/isu', 'message' => 17003], //身份证
            ['address', 'string', 'length' => [1, 50], 'tooShort' => 17004, 'tooLong' => 17004], //地址
            ['customer_origination', 'string', 'length' => [1, 30], 'tooShort' => 17005, 'tooLong' => 17005], //客户来源
            ['company', 'string', 'length' => [1, 50], 'tooShort' => 17006, 'tooLong' => 17006], //单位名称
            ['is_member', 'in', 'range' => [0, 1], 'message' => 17007], //会员状态
            ['is_member', 'default', 'value' => 0],
            ['status', 'in', 'range' => [0, 1], 'message' => 17008], //状态 默认1(0:停用 1:启用)
            ['status', 'default', 'value' => 1],
            ['comment', 'string', 'length' => [0, 200], 'tooLong' => 17009], //备注
            //会员参数
            ['card_number', 'string', 'length' => [1, 30], 'tooShort' => 17045, 'tooLong' => 17045],
            ['type', 'string', 'length' => [1, 30], 'tooShort' => 17046, 'tooLong' => 17046],
            [['price'], 'double', 'min' => 0, 'tooSmall' => 17047, 'message' => 17048],
//            ['price', 'string', 'length' => [0, 30], 'tooLong' => 17049],
            ['customer_comment', 'string', 'length' => [0, 200], 'tooLong' => 17050], //备注
        ];
    }

    /**
     * 添加客户资料和客户汽车信息
     * 二维数组的形式添加
     * $car_info            
     *                      id
     *                      frame_number              车架号
     *                      number_plate_province_id  车牌号省份表ID
     *                      number_plate_alphabet_id  车牌号字母表ID
     *                      number_plate_number       车牌号
     *                      model_id                  汽车型号表ID
     *                      vehicle_displacement      排量
     *                      vehicle_price             车价
     *                      engine_model              发动机型号
     *                      engine_number             发动机号码
     *                      manufacturer              厂牌型号
     *                      leakage_status            漏油检查
     *                      vehicle_license_image_id  上传行驶证照片ID
     *                      other_picture_id          上传其他图片照ID （array）
     *                      next_service_mileage      下次保养里程
     *                      prev_service_mileage      上次保养里程
     *                      tire_status               轮胎检查
     *                      color                     车辆颜色
     *                      break_status              刹车片检查
     *                      break_oil_status          刹车油检查
     *                      bettry_status             电瓶检查
     *                      lubricating_oil_status    机油检查
     *                      insurance_company         保险公司
     *                      fault_light               故障灯检查
     *                      tire_brand_id             轮胎品牌表ID
     *                      tire_specification        轮胎型号
     *                      
     *                      
     * @return bool 
     */
    public function actionModify() {

        try {
            $post_data['id'] = $this->id;
            $post_data['customer_name'] = $this->customer_name;
            $post_data['cellphone_number'] = $this->cellphone_number;
            $post_data['birthday'] = $this->birthday;
            $post_data['ID_number'] = $this->ID_number;
            $post_data['address'] = $this->address;
            $post_data['customer_origination'] = $this->customer_origination;

            if ($this->license_image_name) {
                $post_data['license_image_name'] = Yii::$app->params['OSS_PostHost'] . '/' . $this->license_image_name;
            }

            $post_data['company'] = $this->company;
            $post_data['comment'] = $this->comment;
            $post_data['gender'] = $this->gender;
            $post_data['car_info'] = $this->car_info;

//
//            $car_info[0]['frame_number'] = 'fsdgsdf';
//            $car_info[0]['number_plate_province_id'] = 3;
//            $car_info[0]['number_plate_alphabet_id'] = 2;
//            $car_info[0]['number_plate_number'] = 19452;
//            $car_info[0]['model_id'] = 2711;
//            $car_info[0]['vehicle_displacement'] = '';
//            $car_info[0]['vehicle_price'] = 0.00;
//            $car_info[0]['engine_model'] = '';
//            $car_info[0]['manufacturer'] = '';
//            $car_info[0]['engine_number'] = '';
//            $car_info[0]['leakage_status'] = '';
//            $car_info[0]['next_service_mileage'] = '';
//            $car_info[0]['prev_service_mileage'] = '';
//            $car_info[0]['tire_status'] = '';
//            $car_info[0]['color'] = '';
//            $car_info[0]['break_status'] = '';
//            $car_info[0]['break_oil_status'] = '';
//            $car_info[0]['bettry_status'] = '';
//            $car_info[0]['lubricating_oil_status'] = '';
//            $car_info[0]['insurance_company'] = '';
//            $car_info[0]['fault_light'] = '';
//            $car_info[0]['tire_brand_id'] = '';
//            $car_info[0]['tire_specification'] = '';
//            $post_data['customer_name'] = 'reyrgh';
//            $post_data['cellphone_number'] = 13564419890;
//            $post_data['gender'] = 1;
//            $post_data['birthday'] = '';
//            $post_data['ID_number'] = '';
//            $post_data['address'] = '';
//            $post_data['customer_origination'] = 'sgdfg';
//            $post_data['company'] = '';
//            $post_data['comment'] = '';
//            $post_data['id'] =114;
//            $post_data['car_info']=$car_info;

            //整理参数
            $modify_data = PutModel::prepareData($post_data, false);
            if ($this->gender !== NULL) {
                $modify_data['gender'] = $this->gender;
            }
            $condition['store_id'] = $modify_data['store_id'];
            $condition['status'] = 1;
            $condition['id'] = $this->id;
            unset($modify_data['id']);
//            print_r($condition);
//            die;
            //更改操作
            if (!Update::modifyCustomerInfomation($condition, $modify_data)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17010) {
                $this->addError('modify', 17010);
                return false;
            } elseif ($ex->getCode() === 17011) {
                $this->addError('modify', 17011);
                return false;
            } elseif ($ex->getCode() === 17012) {
                $this->addError('modify', 17012);
                return false;
            } elseif ($ex->getCode() === 17013) {
                $this->addError('modify', 17013);
                return false;
            } elseif ($ex->getCode() === 17014) {
                $this->addError('modify', 17014);
                return false;
            } elseif ($ex->getCode() === 17015) {
                $this->addError('modify', 17015);
                return false;
            } elseif ($ex->getCode() === 17016) {
                $this->addError('modify', 17016);
                return false;
            } elseif ($ex->getCode() === 17017) {
                $this->addError('modify', 17017);
                return false;
            } elseif ($ex->getCode() === 17018) {
                $this->addError('modify', 17018);
                return false;
            } elseif ($ex->getCode() === 17019) {
                $this->addError('modify', 17019);
                return false;
            } elseif ($ex->getCode() === 17020) {
                $this->addError('modify', 17020);
                return false;
            } elseif ($ex->getCode() === 17021) {
                $this->addError('modify', 17021);
                return false;
            } elseif ($ex->getCode() === 17022) {
                $this->addError('modify', 17022);
                return false;
            } elseif ($ex->getCode() === 17023) {
                $this->addError('modify', 17023);
                return false;
            } elseif ($ex->getCode() === 17024) {
                $this->addError('modify', 17024);
                return false;
            } elseif ($ex->getCode() === 17025) {
                $this->addError('modify', 17025);
                return false;
            } elseif ($ex->getCode() === 17026) {
                $this->addError('modify', 17026);
                return false;
            } elseif ($ex->getCode() === 17027) {
                $this->addError('modify', 17027);
                return false;
            } elseif ($ex->getCode() === 17028) {
                $this->addError('modify', 17028);
                return false;
            } elseif ($ex->getCode() === 17029) {
                $this->addError('modify', 17029);
                return false;
            } elseif ($ex->getCode() === 17030) {
                $this->addError('modify', 17030);
                return false;
            } elseif ($ex->getCode() === 17031) {
                $this->addError('modify', 17031);
                return false;
            } elseif ($ex->getCode() === 17032) {
                $this->addError('modify', 17032);
                return false;
            } elseif ($ex->getCode() === 17033) {
                $this->addError('modify', 17033);
                return false;
            } elseif ($ex->getCode() === 17034) {
                $this->addError('modify', 17034);
                return false;
            } elseif ($ex->getCode() === 17035) {
                $this->addError('modify', 17035);
                return false;
            } elseif ($ex->getCode() === 17037) {
                $this->addError('modify', 17037);
                return false;
            } elseif ($ex->getCode() === 17039) {
                $this->addError('modify', 17039);
                return false;
            } elseif ($ex->getCode() === 17059) {
                $this->addError('modify', 17059);
                return false;
            }elseif ($ex->getCode() === 3006) {
                $this->addError('modify', 3006);
                return false;
            } else {
                $this->addError('modify', 17040);
                return false;
            }
        }
    }

    public function actionStop() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_data['status'] = 0;
            $modify_data['last_modified_by'] = current($userIdentity)['id'];
            $modify_data['last_modified_time'] = date('Y-m-d H:i:s');
            //更改操作
            if (Update::modifyAllCustomerInfomation($condition, $modify_data) === false) {
                throw new \Exception('停用客户失败', 17041);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('stop', 3006);
                return false;
            } else {
                $this->addError('stop', 17041);
                return false;
            }
        }
    }

    public function actionOpen() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }

            $condition['id'] = $id;
            $condition['store_id'] = current($userIdentity)['store_id'];

            $modify_data['status'] = 1;
            $modify_data['last_modified_by'] = current($userIdentity)['id'];
            $modify_data['last_modified_time'] = date('Y-m-d H:i:s');
            //更改操作
            if (Update::modifyAllCustomerInfomation($condition, $modify_data) === false) {
                throw new \Exception('启用客户失败', 17042);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('open', 3006);
                return false;
            } else {
                $this->addError('open', 17042);
                return false;
            }
        }
    }

    public function actionIsmember() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();
            $store_id = current($userIdentity)['store_id'];
            $id = $this->id;
            $condition['status'] = 1;
            $condition['id'] = $id;
            $condition['store_id'] = $store_id;
            $customer_infomation_info = Insert::getField($condition, 'is_member');

            if ($customer_infomation_info) {
                if ($customer_infomation_info->is_member !== 0) {
                    throw new \Exception('该客户已经是会员了', 17043);
                }
            } else {
                throw new \Exception('参数错误', 2004);
            }

            $condition_member['card_number'] = $add_data['card_number'] = $this->card_number;
            $add_data['type'] = $this->type;
            $add_data['customer_infomation_id'] = $this->id;
            $condition_member['store_id'] = $add_data['store_id'] = $store_id;
            $add_data['price'] = $this->price;
            $add_data['comment'] = $this->customer_comment;
            $add_data['created_by'] = $add_data['last_modified_by'] = current($userIdentity)['id'];
            $add_data['created_time'] = $add_data['last_modified_time'] = date('Y-m-d H:i:s');

            //卡号验证
            if (InsertMember::getField($condition_member, 'id')) {
                throw new \Exception('会员卡号已存在', 17061);
                return false;
            }

            //更改操作
            if (Update::changeMember($condition, $add_data) === false) {
                throw new \Exception('生成会员失败', 17051);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17043) {
                $this->addError('ismember', 17043);
                return false;
            } elseif ($ex->getCode() === 2004) {
                $this->addError('ismember', 2004);
                return false;
            } elseif ($ex->getCode() === 17061) {
                $this->addError('ismember', 17061);
                return false;
            } else {
                $this->addError('ismember', 17051);
                return false;
            }
        }
    }

    public function actionNomember() {

        try {

            //判断user是否存在
            $userIdentity = PutModel::verifyUser();

            $id = $this->id;
            if (!is_array($id)) {
                throw new \Exception('参数错误,导致更改失败', 3006);
            }
            $condition['status'] = 1;
            $condition['id'] = $id;
            $customer_infomation_info = Insert::getall($condition, 'is_member');
            if (!empty($customer_infomation_info)) {
                foreach ($customer_infomation_info as $key => $value) {
                    if ($customer_infomation_info[$key]['is_member'] != 1) {
                        throw new \Exception('该客户不是会员,无法进行该操作', 17052);
                    }
                }
            } else {
                throw new \Exception('参数错误', 2004);
            }

            //更改操作
            if (Update::delMember($condition) === false) {
                throw new \Exception('取消会员失败', 17053);
                return false;
            }

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 2004) {
                $this->addError('nomember', 2004);
                return false;
            } elseif ($ex->getCode() === 3006) {
                $this->addError('nomember', 3006);
                return false;
            } elseif ($ex->getCode() === 17052) {
                $this->addError('nomember', 17052);
                return false;
            } else {
                $this->addError('nomember', 17053);
                return false;
            }
        }
    }

}
