<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\put;

use common\models\Model as CommonModel;
use commodity\modules\customerinfomation\models\put\db\Insert;
use commodity\modules\customerinfomation\models\put\db\getCarsNumber;
use commodity\modules\customerinfomation\models\put\db\InsertCustomerCars;
use Yii;

class PutModel extends CommonModel {

    const ACTION_INSERT = 'action_insert';

    public $token;
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
    public $suffix = ['gif', 'jpeg', 'png', 'bmp', 'jpg']; //图片格式

    public function scenarios() {
        return [
            self::ACTION_INSERT => [
                'customer_name', 'cellphone_number', 'gender', 'birthday', 'ID_number', 'license_image_name', 'address', 'customer_origination', 'company', 'comment', 'car_info', 'token'
            ],
        ];
    }

    public function rules() {

        return [
            [
                [ 'customer_name', 'cellphone_number', 'gender', 'customer_origination', 'car_info', 'token'],
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
        ];
    }

    /**
     * 添加客户资料和客户汽车信息
     * 二维数组的形式添加
     * $car_info            frame_number              车架号
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
     *                      vehicle_license_image_name  上传行驶证照片
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
    public function actionInsert() {
        try {

            $post_data['customer_name'] = $this->customer_name;
//            $post_data['is_member'] = 0;
            $post_data['cellphone_number'] = $this->cellphone_number;
            $post_data['birthday'] = $this->birthday;
            $post_data['ID_number'] = $this->ID_number;
            $post_data['address'] = $this->address;
            $post_data['customer_origination'] = $this->customer_origination;

            if ($this->license_image_name) {
                $post_data['license_image_name'] = Yii::$app->params['OSS_PostHost'] . '/' . $this->license_image_name;
            }

            $post_data['company'] = $this->company;
//            $post_data['status'] = 1;
            $post_data['comment'] = $this->comment;
            $post_data['gender'] = $this->gender;
            $post_data['car_info'] = $this->car_info;

//            $post_data['car_info']=$car_info; 
//            $car_info[0]['frame_number'] = '发达省份都是';
//            $car_info[0]['number_plate_province_id'] = 3;
//            $car_info[0]['number_plate_alphabet_id'] = 2;
//            $car_info[0]['number_plate_number'] = 123122;
//            $car_info[0]['model_id'] = 993;
//            $car_info[0]['vehicle_displacement'] = 12;
//            $car_info[0]['vehicle_price'] = 212;
//            $car_info[0]['engine_model'] = 1;
//            $car_info[0]['manufacturer'] = 2;
//            $car_info[0]['engine_number'] = 12;
//            $car_info[0]['leakage_status'] = 12;
//            $car_info[0]['next_service_mileage'] = 1; 
//            $car_info[0]['prev_service_mileage'] = 21;
//            $car_info[0]['tire_status'] = 21;
//            $car_info[0]['other_picture_id'] = 'a_88/fe741184e84eb33538c8c5c392f7d8ab635f9c377.png';
//            $car_info[0]['color'] = 2;
//            $car_info[0]['break_status'] = 12;
//            $car_info[0]['break_oil_status'] = 12;
//            $car_info[0]['bettry_status'] = 1;
//            $car_info[0]['lubricating_oil_status'] = 21;
//            $car_info[0]['insurance_company'] = 21;
//            $car_info[0]['fault_light'] = 2;
//            $car_info[0]['tire_brand_id'] = '';
//            $car_info[0]['tire_specification'] = 1;
//            $post_data['customer_name'] = 1;
//            $post_data['cellphone_number'] = 13564411111;
//            $post_data['gender'] = 1;
//            $post_data['birthday'] = '2018-03-15';
//            $post_data['ID_number'] = 610525199205085239;
//            $post_data['address'] = '发的书法大赛';
//            $post_data['customer_origination'] = '发动时发生';
//            $post_data['license_image_id'] = 'C:\fakepath\员工资料工种接口错误.png';
//            $post_data['company'] = 'ADS发大水';
//            $post_data['is_member'] = 1;
//            $post_data['comment'] = 'dfa发大水';
//            $post_data['car_info'] = $car_info;

            $add_data = self::prepareData($post_data);

            $add_data['gender'] = $this->gender;
            $add_data['is_member'] = 0;
            $add_data['status'] = 1;

            //添加操作
            Insert::insertCustomerInfomation($add_data);

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17010) {
                $this->addError('insert', 17010);
                return false;
            } elseif ($ex->getCode() === 17011) {
                $this->addError('insert', 17011);
                return false;
            } elseif ($ex->getCode() === 17012) {
                $this->addError('insert', 17012);
                return false;
            } elseif ($ex->getCode() === 17013) {
                $this->addError('insert', 17013);
                return false;
            } elseif ($ex->getCode() === 17014) {
                $this->addError('insert', 17014);
                return false;
            } elseif ($ex->getCode() === 17015) {
                $this->addError('insert', 17015);
                return false;
            } elseif ($ex->getCode() === 17016) {
                $this->addError('insert', 17016);
                return false;
            } elseif ($ex->getCode() === 17017) {
                $this->addError('insert', 17017);
                return false;
            } elseif ($ex->getCode() === 17018) {
                $this->addError('insert', 17018);
                return false;
            } elseif ($ex->getCode() === 17019) {
                $this->addError('insert', 17019);
                return false;
            } elseif ($ex->getCode() === 17020) {
                $this->addError('insert', 17020);
                return false;
            } elseif ($ex->getCode() === 17021) {
                $this->addError('insert', 17021);
                return false;
            } elseif ($ex->getCode() === 17022) {
                $this->addError('insert', 17022);
                return false;
            } elseif ($ex->getCode() === 17023) {
                $this->addError('insert', 17023);
                return false;
            } elseif ($ex->getCode() === 17024) {
                $this->addError('insert', 17024);
                return false;
            } elseif ($ex->getCode() === 17025) {
                $this->addError('insert', 17025);
                return false;
            } elseif ($ex->getCode() === 17026) {
                $this->addError('insert', 17026);
                return false;
            } elseif ($ex->getCode() === 17027) {
                $this->addError('insert', 17027);
                return false;
            } elseif ($ex->getCode() === 17028) {
                $this->addError('insert', 17028);
                return false;
            } elseif ($ex->getCode() === 17029) {
                $this->addError('insert', 17029);
                return false;
            } elseif ($ex->getCode() === 17030) {
                $this->addError('insert', 17030);
                return false;
            } elseif ($ex->getCode() === 17031) {
                $this->addError('insert', 17031);
                return false;
            } elseif ($ex->getCode() === 17032) {
                $this->addError('insert', 17032);
                return false;
            } elseif ($ex->getCode() === 17033) {
                $this->addError('insert', 17033);
                return false;
            } elseif ($ex->getCode() === 17034) {
                $this->addError('insert', 17034);
                return false;
            } elseif ($ex->getCode() === 17035) {
                $this->addError('insert', 17035);
                return false;
            } elseif ($ex->getCode() === 17037) {
                $this->addError('insert', 17037);
                return false;
            } elseif ($ex->getCode() === 17060) {
                $this->addError('insert', 17060);
                return false;
            } elseif ($ex->getCode() === 17059) {
                $this->addError('insert', 17059);
                return false;
            } else {
                $this->addError('insert', 17036);
                return false;
            }
        }
    }

    public static function prepareData(array $data, $switch = true) {

        //判断user是否存在
        $userIdentity = self::verifyUser();

        //客户参数
        $id = array_key_exists('id', $data) ? $data['id'] : '';
        $customerinfomation_data['customer_name'] = array_key_exists('customer_name', $data) ? $data['customer_name'] : '';

        $customerinfomation_data['store_id'] = $condition['store_id'] = $store_id = current($userIdentity)['store_id'];

        //同一个手机号只能创建一个用户
        $customerinfomation_data['cellphone_number'] = $cellphone_number = $condition['cellphone_number'] = array_key_exists('cellphone_number', $data) ? $data['cellphone_number'] : '';

        if ($cellphone_number) {
            $repeat_customers = Insert::getField($condition, 'id');
            if ($repeat_customers) {
                if (($repeat_customers->id != $id && !$switch) || $switch) {
                    throw new \Exception('同一个手机号码只能创建一个用户,该用户已存在', 17010);
                }
            }
        }

        $customerinfomation_data['gender'] = array_key_exists('gender', $data) ? $data['gender'] : '';

        $birthday = array_key_exists('birthday', $data) ? $data['birthday'] : '';

        if ($birthday) {
            $new_birthday = date('Y-m-d', strtotime($birthday));
            if ($new_birthday == '1970-01-01') {
                throw new \Exception('客户生日日期格式不对。eg:年月日2018-02-04或20180204', 17011);
            }
            $customerinfomation_data['birthday'] = $new_birthday;
        }


        $customerinfomation_data['ID_number'] = array_key_exists('ID_number', $data) ? $data['ID_number'] : '';


        $customerinfomation_data['address'] = array_key_exists('address', $data) ? $data['address'] : '';

        $customerinfomation_data['customer_origination'] = array_key_exists('customer_origination', $data) ? $data['customer_origination'] : '';

        $customerinfomation_data['license_image_name'] = array_key_exists('license_image_name', $data) ? $data['license_image_name'] : '';

        $customerinfomation_data['company'] = array_key_exists('company', $data) ? $data['company'] : '';

        $customerinfomation_data['status'] = array_key_exists('status', $data) ? $data['status'] : '';

        $customerinfomation_data['comment'] = array_key_exists('comment', $data) ? $data['comment'] : '';

        $car_info = array_key_exists('car_info', $data) ? $data['car_info'] : '';
        //汽车信息参数整合
//        $car_info[1]['id'] = 9;
//        $car_info[0]['frame_number'] = '1';
//        $car_info[0]['number_plate_province_id'] = 1;
//        $car_info[0]['number_plate_alphabet_id'] = 2;
//        $car_info[0]['number_plate_number'] = 1;
//        $car_info[0]['model_id'] = 1;
//        $car_info[0]['vehicle_displacement'] = 1;
//        $car_info[0]['vehicle_price'] = 1;
//        $car_info[0]['engine_model'] = 1;
//        $car_info[0]['engine_number'] = 1;
//        $car_info[0]['manufacturer'] = 1;
//        $car_info[0]['leakage_status'] = 1;
//        $car_info[0]['vehicle_license_image_name'] = 1; //上传图片
////        $car_info[0]['other_picture_id'] = 1;//其他图片
//        $car_info[0]['next_service_mileage'] = 1;
//        $car_info[0]['prev_service_mileage'] = 1;
//        $car_info[0]['tire_status'] = 1;
//        $car_info[0]['color'] = 1;
//        $car_info[0]['break_status'] = 1;
//        $car_info[0]['break_oil_status'] = 1;
//        $car_info[0]['bettry_status'] = 1;
//        $car_info[0]['lubricating_oil_status'] = 1;
//        $car_info[0]['insurance_company'] = 1;
//        $car_info[0]['fault_light'] = 1;
//        $car_info[0]['tire_brand_id'] = 1;
//        $car_info[0]['tire_specification'] = 1;
//        $car_info[1]['id'] = 8;
//        $car_info[1]['frame_number'] = '1';
//        $car_info[1]['number_plate_province_id'] = 1;
//        $car_info[1]['number_plate_alphabet_id'] = 1;
//        $car_info[1]['number_plate_number'] = 111;
//        $car_info[1]['model_id'] = 1;
//        $car_info[1]['vehicle_displacement'] = 1;
//        $car_info[1]['vehicle_price'] = 1;
//        $car_info[1]['engine_model'] = 1;
//        $car_info[1]['engine_number'] = 1;
//        $car_info[1]['manufacturer'] = 1;
//        $car_info[1]['leakage_status'] = 1;
//        $car_info[1]['vehicle_license_image_name'] = 1;
////        $car_info[1]['other_picture_id'] = 1;
//        $car_info[1]['next_service_mileage'] = 1;
//        $car_info[1]['prev_service_mileage'] = 1;
//        $car_info[1]['tire_status'] = 1;
//        $car_info[1]['color'] = 1;
//        $car_info[1]['break_status'] = 1;
//        $car_info[1]['break_oil_status'] = 1;
//        $car_info[1]['bettry_status'] = 1;
//        $car_info[1]['lubricating_oil_status'] = 1;
//        $car_info[1]['insurance_company'] = 1;
//        $car_info[1]['fault_light'] = 1;
//        $car_info[1]['tire_brand_id'] = 1;
//        $car_info[1]['tire_specification'] = 1;

        if (count($car_info) && is_array($car_info)) {
            if ($id) {
                $car_condition['store_id'] = $store_id;
                $car_condition['customer_infomation_id'] = $id;

                $customerinfomation_data['car_info'] = self::verifyCarInfo($car_info, $car_condition);
            } else {

                $customerinfomation_data['car_info'] = self::verifyCarInfo($car_info);
            }
        } else {
            throw new \Exception('客户汽车信息有误', 17012);
        }


        $customerinfomation_data['last_modified_by'] = current($userIdentity)['id'];
        $customerinfomation_data['last_modified_time'] = date('Y-m-d H:i:s');

        if ($switch) {
            $customerinfomation_data['created_time'] = date('Y-m-d H:i:s');
            $customerinfomation_data['created_by'] = current($userIdentity)['id'];
        }


        return array_filter($customerinfomation_data, function ($v) {
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
     * 验证汽车信息
     * $car_data 客户汽车信息
     * $customer_id 客户id
     */
    public static function verifyCarInfo(array $car_data, $customer_condition = array()) {

        foreach ($car_data as $key => $value) {

            if (!is_array($value)) {
                throw new \Exception('客户汽车信息有误', 17012);
            }

            if (!empty($customer_condition)) {
                $customer_cars = InsertCustomerCars::getField($customer_condition, 'id');
                if (!$customer_cars) {
                    throw new \Exception('用户车辆信息有误', 17039);
                }
                $car_data[$key]['id'] = $customer_cars->id;
            }


            $frame_number = array_key_exists('frame_number', $value) ? $value['frame_number'] : '';
            $frame_number_len = mb_strlen($frame_number, 'utf8');
            if ($frame_number_len > 30) {
                throw new \Exception('车架号不能超过30个字符', 17013);
            }

            //车牌号
            $number_plate_province_condition['id'] = $number_plate_province_id = array_key_exists('number_plate_province_id', $value) ? $value['number_plate_province_id'] : '';
            if ($number_plate_province_id) {
                if (!$cars_province=getCarsNumber::getProvince($number_plate_province_condition, 'id,name')) {
                    throw new \Exception('车牌号省份参数有误', 17014);
                }
            } else {
                throw new \Exception('车牌号省份参数有误', 17014);
            }

            $number_plate_alphabet_condition['id'] = $number_plate_alphabet_id = array_key_exists('number_plate_alphabet_id', $value) ? $value['number_plate_alphabet_id'] : '';
            if ($number_plate_alphabet_id) {
                if (!$cars_alphabet=getCarsNumber::getAlphabet($number_plate_alphabet_condition, 'id,name')) {
                    throw new \Exception('车牌号字母参数有误', 17015);
                }
            } else {
                throw new \Exception('车牌号字母参数有误', 17015);
            }

            $number_plate_number = array_key_exists('number_plate_number', $value) ? $value['number_plate_number'] : '';
            $number_plate_number_len = mb_strlen($number_plate_number, 'utf8');
            if ($number_plate_number_len != 5 && $number_plate_number_len != 6) {
                throw new \Exception('车牌号必须是5位或者6位', 17059);
            } elseif (!$number_plate_number) {
                throw new \Exception('车牌号不能为空', 17016);
            }
            
           $car_data[$key]['plate_number']=$cars_province->name.$cars_alphabet->name.$number_plate_number;

            //汽车型号表ID
            $model_condition['id'] = $model_id = array_key_exists('model_id', $value) ? $value['model_id'] : '';
            if ($model_id) {
                if (!getCarsNumber::getCarStyleHome($model_condition, 'id')) {
                    throw new \Exception('汽车型号参数有误', 17017);
                }
            } else {
                throw new \Exception('汽车型号不能为空', 17060);
            }

            //排量
            $vehicle_displacement = array_key_exists('vehicle_displacement', $value) ? $value['vehicle_displacement'] : '';
            $vehicle_displacement_len = mb_strlen($vehicle_displacement, 'utf8');
            if ($vehicle_displacement_len > 30) {
                throw new \Exception('排量不能超过30个字符', 17018);
            }

            //车价
            $car_data[$key]['vehicle_price'] = $vehicle_price = array_key_exists('vehicle_price', $value) ? round($value['vehicle_price'], 2) : '';
            $vehicle_price_len = mb_strlen($vehicle_price, 'utf8');
            if ($vehicle_displacement_len > 10) {
                throw new \Exception('车价不能超过10个字符', 17019);
            }

            //发动机型号
            $engine_model = array_key_exists('engine_model', $value) ? $value['engine_model'] : '';
            $engine_model_len = mb_strlen($engine_model, 'utf8');
            if ($engine_model_len > 30) {
                throw new \Exception('发动机型号不能超过30个字符', 17020);
            }

            //厂牌型号
            $manufacturer = array_key_exists('manufacturer', $value) ? $value['manufacturer'] : '';
            $manufacturer_len = mb_strlen($manufacturer, 'utf8');
            if ($manufacturer_len > 30) {
                throw new \Exception('厂牌型号不能超过30个字符', 17021);
            }

            //发动机号码
            $engine_number = array_key_exists('engine_number', $value) ? $value['engine_number'] : '';
            $engine_number_len = mb_strlen($engine_number, 'utf8');
            if ($engine_number_len > 30) {
                throw new \Exception('发动机号码不能超过30个字符', 17022);
            }

            //漏油检查
            $leakage_status = array_key_exists('leakage_status', $value) ? $value['leakage_status'] : '';
            $leakage_status_len = mb_strlen($leakage_status, 'utf8');
            if ($leakage_status_len > 30) {
                throw new \Exception('漏油检查不能超过30个字符', 17023);
            }

            //上传行驶证照片
            $vehicle_license_image_name = array_key_exists('vehicle_license_image_name', $value) ? $value['vehicle_license_image_name'] : '';
            if ($vehicle_license_image_name) {
                $car_data[$key]['vehicle_license_image_name'] = Yii::$app->params['OSS_PostHost'] . '/' . $vehicle_license_image_name;
            }

            //下次保养里程
            $next_service_mileage = array_key_exists('next_service_mileage', $value) ? $value['next_service_mileage'] : '';
            $next_service_mileage_len = mb_strlen($next_service_mileage, 'utf8');
            if ($next_service_mileage_len > 10) {
                throw new \Exception('下次保养里程不能超过10个字符', 17024);
            }

            //上次保养里程
            $prev_service_mileage = array_key_exists('prev_service_mileage', $value) ? $value['prev_service_mileage'] : '';
            $prev_service_mileage_len = mb_strlen($prev_service_mileage, 'utf8');
            if ($prev_service_mileage_len > 10) {
                throw new \Exception('上次保养里程不能超过10个字符', 17025);
            }

            //轮胎检查
            $tire_status = array_key_exists('tire_status', $value) ? $value['tire_status'] : '';
            $tire_status_len = mb_strlen($tire_status, 'utf8');
            if ($tire_status_len > 30) {
                throw new \Exception('轮胎检查不能超过30个字符', 17026);
            }

            //上传其他图片
          
            //车辆颜色
            $color = array_key_exists('color', $value) ? $value['color'] : '';
            $color_len = mb_strlen($color, 'utf8');
            if ($color_len > 10) {
                throw new \Exception('车辆颜色不能超过10个字符', 17027);
            }

            //刹车片检查
            $break_status = array_key_exists('break_status', $value) ? $value['break_status'] : '';
            $break_status_len = mb_strlen($break_status, 'utf8');
            if ($break_status_len > 30) {
                throw new \Exception('刹车片检查不能超过30个字符', 17028);
            }

            //刹车油检查
            $break_oil_status = array_key_exists('break_oil_status', $value) ? $value['break_oil_status'] : '';
            $break_oil_status_len = mb_strlen($break_oil_status, 'utf8');
            if ($break_oil_status_len > 30) {
                throw new \Exception('刹车油检查不能超过30个字符', 17029);
            }

            //电瓶检查
            $bettry_status = array_key_exists('bettry_status', $value) ? $value['bettry_status'] : '';
            $bettry_status_len = mb_strlen($bettry_status, 'utf8');
            if ($bettry_status_len > 30) {
                throw new \Exception('电瓶检查不能超过30个字符', 17030);
            }

            //机油检查
            $lubricating_oil_status = array_key_exists('lubricating_oil_status', $value) ? $value['lubricating_oil_status'] : '';
            $lubricating_oil_status_len = mb_strlen($lubricating_oil_status, 'utf8');
            if ($lubricating_oil_status_len > 30) {
                throw new \Exception('机油检查不能超过30个字符', 17031);
            }

            //保险公司
            $insurance_company = array_key_exists('insurance_company', $value) ? $value['insurance_company'] : '';
            $insurance_company_len = mb_strlen($insurance_company, 'utf8');
            if ($insurance_company_len > 30) {
                throw new \Exception('保险公司不能超过30个字符', 17032);
            }

            //故障灯检查
            $fault_light = array_key_exists('fault_light', $value) ? $value['fault_light'] : '';
            $fault_light_len = mb_strlen($fault_light, 'utf8');
            if ($fault_light_len > 30) {
                throw new \Exception('故障灯检查不能超过30个字符', 17033);
            }

            //轮胎品牌表ID
            $tire_brand_condition['id'] = $tire_brand_id = array_key_exists('tire_brand_id', $value) ? $value['tire_brand_id'] : NULL;
            if ($tire_brand_id) {
                if (!getCarsNumber::getCarBrandHome($tire_brand_condition, 'id')) {
                    throw new \Exception('轮胎品牌参数有误', 17034);
                }
            }
            $car_data[$key]['tire_brand_id'] = $tire_brand_id;

            //轮胎型号
            $tire_specification = array_key_exists('tire_specification', $value) ? $value['tire_specification'] : '';
            $tire_specification_len = mb_strlen($tire_specification, 'utf8');
            if ($tire_specification_len > 30) {
                throw new \Exception('轮胎型号不能超过30个字符', 17035);
            }
        }

        return $car_data;
    }

}
