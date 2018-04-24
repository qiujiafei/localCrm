<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\import\models\data;

use common\models\Model as CommonModel;
use commodity\modules\import\models\data\db\Insert;
use common\components\upload\services\GeneralInvokableFactory;
use commodity\modules\service\models\put\db\Get as ServiceGet;
use commodity\modules\service\models\put\db\Insert as ServiceInsert;
use commodity\modules\service\models\put\PutModel as ServicePutModel;
use commodity\modules\import\models\data\db\getCommodity;
use commodity\modules\import\models\data\db\getCommodityOriginate;
use commodity\modules\import\models\data\db\getDepot;
use commodity\modules\classification\models\put\db\Insert as classificationInsert;
use commodity\modules\commodityUnit\models\put\db\Insert as unitInsert;
use commodity\modules\customerinfomation\models\put\db\Insert as customerinfomationInsert;
use commodity\modules\customerinfomation\models\put\db\getCarsNumber;
use moonland\phpexcel\Excel;
use common\exceptions\Exception;
use common\ActiveRecord\ServiceAR;
use common\ActiveRecord\CommodityAR;
use Yii;

class DataModel extends CommonModel {

    const ACTION_CUSTORMER = 'action_customer';
    const ACTION_SERVICE = 'action_service';
    const ACTION_COMMODITY = 'action_commodity';

    public $token;
    public $suffix = ['xlsx', 'xls'];

    public function scenarios() {
        return [
            self::ACTION_CUSTORMER => [
                'token'
            ],
            self::ACTION_SERVICE => [
                'token'
            ],
            self::ACTION_COMMODITY => [
                'token'
            ],
        ];
    }

    /**
     * 导入客户模块接口
     * 
     * @return bool 
     */
    public function actionCustomer() {

        try {

            $fileManager = (new GeneralInvokableFactory)();
            $file_data = $fileManager->getInfo();
            $file_path = $file_data['file'][0]['name'];
            $file_info = pathinfo($file_path);
            if (array_key_exists('extension', $file_info)) {
                $file_suffix = $file_info['extension'];
            } else {
                throw new Exception('导入文件格式有误');
            }


            if (!in_array($file_suffix, $this->suffix)) {
                throw new Exception('文件类型必须是excel文件');
//                $this->addError('actionCustomer', new Exception('文件类型必须是excel文件'));
//                return false;
            }
            $data = Excel::import($file_path);


//            $data = Excel::import('C:\wamp64\www\wx\crm\upload\customer.xlsx');

            if (empty($data)) {
                throw new Exception('导入数据不能为空');
            }


            $title_data = [
                '客户姓名' => 'customer_name',
                '性别' => 'gender',
                '地址' => 'address',
                '身份证' => 'ID_number',
                '生日' => 'birthday',
                '手机号码' => 'cellphone_number',
                '客户来源' => 'customer_origination',
                '单位名称' => 'company',
                '备注' => 'comment',
                '车架号' => 'frame_number',
                '车牌号省份' => 'number_plate_province_name',
                '车牌号字母' => 'number_plate_alphabet_name',
                '车牌号' => 'number_plate_number',
                '汽车型号' => 'model_name',
                '排量' => 'vehicle_displacement',
                '车价' => 'vehicle_price',
                '发动机型号' => 'engine_model',
                '发动机号码' => 'engine_number',
                '厂牌型号' => 'manufacturer',
                '漏油检查' => 'leakage_status',
                '下次保养里程' => 'next_service_mileage',
                '上次保养里程' => 'prev_service_mileage',
                '轮胎检查' => 'tire_status',
                '车辆颜色' => 'color',
                '刹车片检查' => 'break_status',
                '刹车油检查' => 'break_oil_status',
                '电瓶检查' => 'bettry_status',
                '机油检查' => 'lubricating_oil_status',
                '保险公司' => 'insurance_company',
                '故障灯检查' => 'fault_light',
                '轮胎品牌' => 'tire_brand_name',
                '轮胎型号' => 'tire_specification',
            ];
            $data_key = array_keys($data[0]);
            $title_key = array_keys($title_data);

            if (count($data_key) != count($title_key)) {
                throw new Exception('导入数据模板有误');
            } else {
                foreach ($data_key as $d_v) {
                    if (!in_array($d_v, $title_key)) {
                        throw new Exception('导入数据模板有误');
                    }
                }
            }

            $customer_key = [
                '客户姓名' => 'customer_name',
                '性别' => 'gender',
                '地址' => 'address',
                '身份证' => 'ID_number',
                '生日' => 'birthday',
                '手机号码' => 'cellphone_number',
                '客户来源' => 'customer_origination',
                '单位名称' => 'company',
                '备注' => 'comment',
            ];
            foreach ($data as $k => $onecustomer) {
                foreach ($onecustomer as $key => $value) {
                    if (in_array($title_data[$key], $customer_key)) {
                        $customer_data[$k][$title_data[$key]] = $value;
                    } else {
                        $customer_data[$k]['car_info'][$title_data[$key]] = $value;
                    }
                }
                $customer_data[$k] = self::prepareCustomerData($customer_data[$k], $k + 1);
                $car_sum[] = $customer_data[$k]['car_info'][0]['number_plate_province_id'] . $customer_data[$k]['car_info'][0]['number_plate_alphabet_id'] . $customer_data[$k]['car_info'][0]['number_plate_number'];
                $cellphone_number[] = $customer_data[$k]['cellphone_number'];
            }

            $cellphone_number = array_unique($cellphone_number);
            if (count($cellphone_number) != count($data)) {
                throw new Exception('导入的客户中有重复的');
            }

            $car_sum = array_unique($car_sum);
            if (count($car_sum) != count($data)) {
                throw new Exception('导入的客户中车牌有重复的');
            }

            $transaction = Yii::$app->db->beginTransaction();
            foreach ($customer_data as $customerinfo) {
                if (customerinfomationInsert::insertCustomerInfomation($customerinfo) === false) {
                    throw new Exception('导入客户资料失败');
                    $transaction->rollback();
                    return false;
                }
            }
            $transaction->commit();

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17037) {
                $this->addError('importcustomer', 17037);
                return false;
            } else {
                $this->addError('importcustomer', $ex);
                return false;
            }
        }
    }

    public static function prepareCustomerData(array $data, $row) {

        $userIdentity = self::verifyUser();
        $store_id = current($userIdentity)['store_id'];

        $customer_name = $data['customer_name']? : '';
        $customer_name_len = mb_strlen($customer_name, 'utf8');
        if ($customer_name_len > 30) {
            throw new Exception('第' . $row . '行' . '客户名称不能超过30个字符');
        } elseif ($customer_name_len <= 0) {
            throw new Exception('第' . $row . '行' . '客户名称不能少于1个字符');
        }
        $customer_data['customer_name'] = $customer_name;

        $cellphone_number = $data['cellphone_number']? : '';
        if ($cellphone_number && !preg_match("/^1[3456789]\d{9}$/ims", $cellphone_number)) {
            throw new Exception('第' . $row . '行' . '客户手机号码格式有误');
        } elseif (!$cellphone_number) {
            throw new Exception('第' . $row . '行' . '客户手机号码不能为空');
        }
        $condition['cellphone_number'] = $cellphone_number;
        $condition['store_id'] = $store_id;
        $repeat_customers = customerinfomationInsert::getField($condition, 'id');
        if ($repeat_customers) {
            throw new Exception('同一个手机号码只能创建一个用户,该用户已存在');
        }
        $customer_data['cellphone_number'] = $cellphone_number;

        $gender = array_key_exists('gender', $data) ? $data['gender'] : NULL;
        if ($gender) {
            if ($gender == '女') {
                $gender = 0;
            } elseif ($gender == '男') {
                $gender = 1;
            } elseif ($gender == '其他') {
                $gender = 2;
            } else {
                throw new Exception('第' . $row . '行' . '客户性别参数有误');
            }
        } else {
            throw new Exception('第' . $row . '行' . '客户性别不能为空');
        }

        $customer_data['gender'] = $gender;

        $customer_origination = $data['customer_origination']? : '';
        $customer_origination_len = mb_strlen($customer_origination, 'utf8');
        if ($customer_origination_len > 30) {
            throw new Exception('第' . $row . '行' . '客户来源不能超过30个字符');
        } elseif ($customer_origination_len <= 0) {
            throw new Exception('第' . $row . '行' . '客户来源不能少于1个字符');
        }
        $customer_data['customer_origination'] = $customer_origination;

        $address = $data['address']? : '';
        $address_len = mb_strlen($address, 'utf8');
        if ($address_len > 50) {
            throw new Exception('第' . $row . '行' . '客户地址不能超过50个字符(1个中文或1个英文相当于1个字符)');
        }
        $customer_data['address'] = $address;

        $ID_number = $data['ID_number']? : '';
        if ($ID_number && !preg_match("/^([\d]{17}[xX\d]|[\d]{15})$/isu", $ID_number)) {
            throw new Exception('第' . $row . '行' . '客户身份证格式有误');
        }
        $customer_data['ID_number'] = $ID_number;

        $customer_origination = $data['customer_origination']? : '';
        $customer_origination_len = mb_strlen($customer_origination, 'utf8');
        if ($customer_origination_len > 30) {
            throw new Exception('第' . $row . '行' . '客户来源不能超过30个字符(1个中文或1个英文相当于1个字符)');
        }
        $customer_data['customer_origination'] = $customer_origination;

        $company = $data['company']? : '';
        $company_len = mb_strlen($company, 'utf8');
        if ($company_len > 50) {
            throw new Exception('第' . $row . '行' . '单位名称不能超过50个字符(1个中文或1个英文相当于1个字符)');
        }
        $customer_data['company'] = $company;


        $comment = $data['comment']? : '';
        $comment_len = mb_strlen($comment, 'utf8');
        if ($comment_len > 200) {
            throw new Exception('第' . $row . '行' . '服务项目备注不能超过200个字符(1个中文或1个英文相当于1个字符)');
        }
        $customer_data['comment'] = $comment;

        $customer_data['last_modified_by'] = current($userIdentity)['id'];
        $customer_data['last_modified_time'] = date('Y-m-d H:i:s');

        $customer_data['created_time'] = date('Y-m-d H:i:s');
        $customer_data['created_by'] = current($userIdentity)['id'];
        $customer_data['store_id'] = $store_id;
        //汽车信息验证
        $car_info[0] = array_key_exists('car_info', $data) ? $data['car_info'] : '';
        if (count($car_info) && is_array($car_info)) {
            $customer_data['car_info'] = self::verifyCarInfo($car_info, $row);
        } else {
            throw new Exception('客户汽车信息有误');
        }

        return $customer_data;
    }

    /**
     * 验证汽车信息
     * $car_data 客户汽车信息
     * $customer_id 客户id
     */
    public static function verifyCarInfo(array $car_data, $row) {

        foreach ($car_data as $key => $value) {

            $frame_number = $value['frame_number']? : '';
            $frame_number_len = mb_strlen($frame_number, 'utf8');
            if ($frame_number_len > 30) {
                throw new Exception('第' . $row . '行' . '车架号不能超过30个字符');
            }

            //车牌号
            $number_plate_province_condition['name'] = $number_plate_province_name = $value['number_plate_province_name']? : '';
            if ($number_plate_province_name) {
                if (!$number_plate_province = getCarsNumber::getProvince($number_plate_province_condition, 'id')) {
                    throw new Exception('第' . $row . '行' . '车牌号省份参数有误');
                }
                $car_data[$key]['number_plate_province_id'] = $number_plate_province['id'];
                unset($car_data[$key]['number_plate_province_name']);
            } else {
                throw new Exception('第' . $row . '行' . '车牌号省份参数有误');
            }

            $number_plate_alphabet_condition['name'] = $number_plate_alphabet_name = $value['number_plate_alphabet_name']? : '';
            if ($number_plate_alphabet_name) {
                if (!$number_plate_alphabet = getCarsNumber::getAlphabet($number_plate_alphabet_condition, 'id')) {
                    throw new Exception('第' . $row . '行' . '车牌号字母参数有误');
                }
                $car_data[$key]['number_plate_alphabet_id'] = $number_plate_alphabet['id'];
                unset($car_data[$key]['number_plate_alphabet_name']);
            } else {
                throw new Exception('第' . $row . '行' . '车牌号字母参数有误');
            }

            $number_plate_number = $value['number_plate_number']? : '';
            $number_plate_number_len = mb_strlen($number_plate_number, 'utf8');
            if ($number_plate_number_len != 5 && $number_plate_number_len != 6) {
                throw new \Exception('第' . $row . '行' . '车牌号必须是5位或者6位');
            } elseif (!$number_plate_number_len) {
                throw new \Exception('第' . $row . '行' . '车牌号不能为空');
            }

            //汽车型号表ID
            $model_condition['name'] = $model_name = $value['model_name']? : '';

            if ($model_name) {
                if (!$model_data = getCarsNumber::getCarStyleHome($model_condition, 'id')) {
                    throw new Exception('第' . $row . '行' . '汽车型号参数有误');
                }
                $car_data[$key]['model_id'] = $model_data['id'];
                unset($car_data[$key]['model_name']);
            } else {
                throw new Exception('第' . $row . '行' . '汽车型号不能为空');
            }

            //排量
            $vehicle_displacement = $value['vehicle_displacement']? : '';
            $vehicle_displacement_len = mb_strlen($vehicle_displacement, 'utf8');
            if ($vehicle_displacement_len > 30) {
                throw new Exception('第' . $row . '行' . '排量不能超过30个字符');
            }

            //车价
            $car_data[$key]['vehicle_price'] = $vehicle_price = $value['vehicle_price'] ? round($value['vehicle_price'], 2) : '';
            $vehicle_price_len = mb_strlen($vehicle_price, 'utf8');
            if ($vehicle_displacement_len > 10) {
                throw new Exception('第' . $row . '行' . '车价不能超过10个字符');
            }

            //发动机型号
            $engine_model = $value['engine_model']? : '';
            $engine_model_len = mb_strlen($engine_model, 'utf8');
            if ($engine_model_len > 30) {
                throw new Exception('第' . $row . '行' . '发动机型号不能超过30个字符');
            }

            //厂牌型号
            $manufacturer = $value['manufacturer']? : '';
            $manufacturer_len = mb_strlen($manufacturer, 'utf8');
            if ($manufacturer_len > 30) {
                throw new Exception('第' . $row . '行' . '厂牌型号不能超过30个字符');
            }

            //发动机号码
            $engine_number = $value['engine_number']? : '';
            $engine_number_len = mb_strlen($engine_number, 'utf8');
            if ($engine_number_len > 30) {
                throw new Exception('第' . $row . '行' . '发动机号码不能超过30个字符');
            }

            //漏油检查
            $leakage_status = $value['leakage_status']? : '';
            $leakage_status_len = mb_strlen($leakage_status, 'utf8');
            if ($leakage_status_len > 30) {
                throw new Exception('第' . $row . '行' . '漏油检查不能超过30个字符');
            }

            //上传行驶证照片ID
            //下次保养里程
            $next_service_mileage = $value['next_service_mileage'] ? : '';
            $next_service_mileage_len = mb_strlen($next_service_mileage, 'utf8');
            if ($next_service_mileage_len > 10) {
                throw new \Exception('下次保养里程不能超过10个字符', 17024);
            }

            //上次保养里程
            $prev_service_mileage = $value['prev_service_mileage'] ? : '';
            $prev_service_mileage_len = mb_strlen($prev_service_mileage, 'utf8');
            if ($prev_service_mileage_len > 10) {
                throw new Exception('第' . $row . '行' . '上次保养里程不能超过10个字符');
            }

            //轮胎检查
            $tire_status = $value['tire_status']? : '';
            $tire_status_len = mb_strlen($tire_status, 'utf8');
            if ($tire_status_len > 30) {
                throw new Exception('第' . $row . '行' . '轮胎检查不能超过30个字符');
            }

            //上传其他图片
            //车辆颜色
            $color = $value['color']? : '';
            $color_len = mb_strlen($color, 'utf8');
            if ($color_len > 10) {
                throw new Exception('第' . $row . '行' . '车辆颜色不能超过10个字符');
            }

            //刹车片检查
            $break_status = $value['break_status']? : '';
            $break_status_len = mb_strlen($break_status, 'utf8');
            if ($break_status_len > 30) {
                throw new Exception('第' . $row . '行' . '刹车片检查不能超过30个字符');
            }

            //刹车油检查
            $break_oil_status = $value['break_oil_status']? : '';
            $break_oil_status_len = mb_strlen($break_oil_status, 'utf8');
            if ($break_oil_status_len > 30) {
                throw new Exception('第' . $row . '行' . '刹车油检查不能超过30个字符');
            }

            //电瓶检查
            $bettry_status = $value['bettry_status']? : '';
            $bettry_status_len = mb_strlen($bettry_status, 'utf8');
            if ($bettry_status_len > 30) {
                throw new Exception('第' . $row . '行' . '电瓶检查不能超过30个字符');
            }

            //机油检查
            $lubricating_oil_status = $value['lubricating_oil_status']? : '';
            $lubricating_oil_status_len = mb_strlen($lubricating_oil_status, 'utf8');
            if ($lubricating_oil_status_len > 30) {
                throw new Exception('第' . $row . '行' . '机油检查不能超过30个字符');
            }

            //保险公司
            $insurance_company = $value['insurance_company']? : '';
            $insurance_company_len = mb_strlen($insurance_company, 'utf8');
            if ($insurance_company_len > 30) {
                throw new Exception('第' . $row . '行' . '保险公司不能超过30个字符');
            }

            //故障灯检查
            $fault_light = $value['fault_light']? : '';
            $fault_light_len = mb_strlen($fault_light, 'utf8');
            if ($fault_light_len > 30) {
                throw new Exception('第' . $row . '行' . '故障灯检查不能超过30个字符');
            }

            //轮胎品牌
            $tire_brand_condition['name'] = $tire_brand_name = $value['tire_brand_name']? : '';
            $tire_brand['id'] = NULL;
            if ($tire_brand_name) {
                if (!$tire_brand = getCarsNumber::getCarBrandHome($tire_brand_condition, 'id')) {
                    throw new Exception('第' . $row . '行' . '轮胎品牌参数有误');
                }
            }
            $car_data[$key]['tire_brand_id'] = $tire_brand['id'];
            unset($car_data[$key]['tire_brand_name']);

            //轮胎型号
            $tire_specification = $value['tire_specification']? : '';
            $tire_specification_len = mb_strlen($tire_specification, 'utf8');
            if ($tire_specification_len > 30) {
                throw new Exception('第' . $row . '行' . '轮胎型号不能超过30个字符');
            }
        }

        return $car_data;
    }

    /**
     * 导入服务模块接口
     * 
     * @return bool 
     */
    public function actionService() {

        try {

            $fileManager = (new GeneralInvokableFactory)();
            $file_data = $fileManager->getInfo();
            $file_path = $file_data['file'][0]['name'];
            $file_info = pathinfo($file_path);
            if (array_key_exists('extension', $file_info)) {
                $file_suffix = $file_info['extension'];
            } else {
                throw new Exception('导入文件格式有误');
            }

            if (!in_array($file_suffix, $this->suffix)) {
                throw new Exception('文件类型必须是excel文件');
            }
            $data = Excel::import($file_path);


//            $data = Excel::import('C:\wamp64\www\wx\crm\upload\service.xlsx');

            if (empty($data)) {
                throw new Exception('导入数据不能为空');
            }


            $title_data = [
                '服务项目名称' => 'service_name',
                '规格' => 'specification',
                '销售价格' => 'price',
                '自助项目' => 'type',
                '服务项目分类' => 'service_claasification_name',
                '备注' => 'comment',
            ];
            $data_key = array_keys($data[0]);
            $title_key = array_keys($title_data);

            if (count($data_key) != count($title_key)) {
                throw new Exception('导入数据模板有误');
            } else {
                foreach ($data_key as $d_v) {
                    if (!in_array($d_v, $title_key)) {
                        throw new Exception('导入数据模板有误');
                    }
                }
            }

            $service_count = count($data);
            $service_code = self::getServiceCode($service_count);


            foreach ($data as $k => $oneservice) {
                foreach ($oneservice as $key => $value) {
                    $service_data[$k][$title_data[$key]] = $value;
                }
                $service_data[$k] = self::prepareServiceData($service_data[$k], $k + 1);
                $service_data[$k]['service_code'] = $service_code[$k];
                $service_name[] = $service_data[$k]['service_name'];
            }

            $service_name = array_unique($service_name);


            if (count($service_name) != $service_count) {
                throw new Exception('导入的服务名称有重复的');
            }

            $import_data = Yii::$app->db->createCommand()->batchInsert(ServiceAR::tableName(), array_keys($service_data[0]), $service_data
                    )->execute();

            if ($import_data === false) {
                throw new Exception('导入的服务失败');
            }
            return [];
        } catch (\Exception $ex) {
            $this->addError('importservice', $ex);
            return false;
        }
    }

    public static function getServiceCode($sum) {

        $userIdentity = self::verifyUser();
        $store_id = current($userIdentity)['store_id'];

        for ($i = 0; $i < $sum; $i++) {
            $condition['store_id'] = $store_id;
            $service_code[] = ServicePutModel::getServiceCode(8, $condition);
            if ($i == $sum - 1) {
                $service_code = array_unique($service_code);

                if (count($service_code) != $sum) {
                    $service_code = self::getServiceCode($sum);
                }
                return $service_code;
            }
        }
    }

    public static function prepareServiceData(array $data, $row) {

        $userIdentity = self::verifyUser();
        $store_id = current($userIdentity)['store_id'];

        $service_name = $data['service_name']? : '';
        $service_name_len = mb_strlen($service_name, 'utf8');
        if ($service_name_len > 30) {
            throw new Exception('第' . $row . '行' . '服务名称不能超过30个字符');
        } elseif ($service_name_len <= 0) {
            throw new Exception('第' . $row . '行' . '服务名称不能少于1个字符');
        }
        //服务项目名称是否重复
        $condition['store_id'] = $store_id;
        $condition['service_name'] = $service_name;
        $info = ServiceInsert::getField($condition, 'id');
        if ($info) {
            throw new Exception('第' . $row . '行' . '服务项目名称已存在');
        }
        $service_data['service_name'] = $service_name;



        $specification = $data['specification']? : '';
        $specification_len = mb_strlen($specification, 'utf8');
        if ($specification_len > 30) {
            throw new Exception('第' . $row . '行' . '规格不能超过30个字符');
        } elseif ($specification_len <= 0) {
            throw new Exception('第' . $row . '行' . '规格不能少于1个字符');
        }
        $service_data['specification'] = $specification;

        $service_claasification_name = $data['service_claasification_name']? : '';
        if ($service_claasification_name) {
            $claasification_condition['classification_name'] = $service_claasification_name;
            $claasification_condition['status'] = 1;
            $claasification_condition['store_id'] = $store_id;
            $claasification_info = ServiceGet::getField($claasification_condition, 'id');
            if (!$claasification_info) {
                throw new Exception('第' . $row . '行' . '服务项目分类参数有误');
            }
            $service_data['service_claasification_name'] = $service_claasification_name;
            $service_data['service_claasification_id'] = $claasification_info['id'];
        } else {
            throw new Exception('第' . $row . '行' . '服务项目分类不能为空');
        }

        $service_data['price'] = array_key_exists('price', $data) ? round($data['price'], 2) : 0.00;

        $type = array_key_exists('type', $data) ? $data['type'] : 0;
        if ($type) {
            if ($type == '非自助') {
                $type = 0;
            } elseif ($type == '自助') {
                $type = 1;
            } else {
                throw new Exception('第' . $row . '行' . '自助项目参数有误');
            }
        } else {
            $type = 0;
        }

        $service_data['type'] = $type;


        $comment = $data['comment']? : '';
        $comment_len = mb_strlen($comment, 'utf8');
        if ($comment_len > 100) {
            throw new Exception('第' . $row . '行' . '服务项目备注不能超过100个字符(1个中文或1个英文相当于1个字符)');
        }
        $service_data['comment'] = $comment;

        $service_data['last_modified_by'] = current($userIdentity)['id'];
        $service_data['last_modified_time'] = date('Y-m-d H:i:s');

        $service_data['created_time'] = date('Y-m-d H:i:s');
        $service_data['created_by'] = current($userIdentity)['id'];
        $service_data['store_id'] = $store_id;
        return $service_data;
    }

    /**
     * 导入商品模块接口
     * 
     * @return bool 
     */
    public function actionCommodity() {


        try {

            $fileManager = (new GeneralInvokableFactory)();
            $file_data = $fileManager->getInfo();
            $file_path = $file_data['file'][0]['name'];
            $file_info = pathinfo($file_path);
            if (array_key_exists('extension', $file_info)) {
                $file_suffix = $file_info['extension'];
            } else {
                throw new Exception('导入文件格式有误');
            }

            if (!in_array($file_suffix, $this->suffix)) {
                throw new Exception('文件类型必须是excel文件');
            }
            $data = Excel::import($file_path);


//            $data = Excel::import('C:\wamp64\www\wx\crm\upload\commodity.xlsx');

            if (empty($data)) {
                throw new Exception('导入数据不能为空');
            }


            $title_data = [
                '商品名称' => 'commodity_name',
                '商品规格' => 'specification',
                '商品编码' => 'commodity_code',
                '分类名称' => 'classification_name',
                '价格' => 'price',
                '条形码' => 'barcode',
                '单位名称' => 'unit_name',
                '配件属性' => 'commodity_property_name',
                '备注' => 'comment',
                '来源' => 'commodity_originate_name',
                '默认仓库' => 'default_depot_name',
            ];
            $data_key = array_keys($data[0]);
            $title_key = array_keys($title_data);

            if (count($data_key) != count($title_key)) {
                throw new Exception('导入数据模板有误');
            } else {
                foreach ($data_key as $d_v) {
                    if (!in_array($d_v, $title_key)) {
                        throw new Exception('导入数据模板有误');
                    }
                }
            }

            foreach ($data as $k => $onecommodity) {

                foreach ($onecommodity as $key => $value) {
                    $commodity_data[$k][$title_data[$key]] = $value;
                }
                $commodity_data[$k] = self::prepareCommodityData($commodity_data[$k], $k + 1);
                $commodity_name[] = $commodity_data[$k]['commodity_name'];
            }

            $commodity_name = array_unique($commodity_name);

            if (count($commodity_name) != count($data)) {
                throw new Exception('导入的商品名称有重复的');
            }

            $import_data = Yii::$app->db->createCommand()->batchInsert(CommodityAR::tableName(), array_keys($commodity_data[0]), $commodity_data
                    )->execute();

            if ($import_data === false) {
                throw new Exception('导入的商品失败');
            }
            return [];
        } catch (\Exception $ex) {
            $this->addError('importcommodity', $ex);
            return false;
        }
    }

    public static function prepareCommodityData(array $data, $row) {

        $userIdentity = self::verifyUser();
        $store_id = current($userIdentity)['store_id'];

        $commodity_name = $data['commodity_name']? : '';
        $commodity_name_len = mb_strlen($commodity_name, 'utf8');
        if ($commodity_name_len > 30) {
            throw new Exception('第' . $row . '行' . '商品名称不能超过30个字符');
        } elseif ($commodity_name_len <= 0) {
            throw new Exception('第' . $row . '行' . '商品名称不能少于1个字符');
        }

        //服务项目名称是否重复
        $condition['store_id'] = $store_id;
        $condition['commodity_name'] = $commodity_name;
        $info = getCommodity::getField($condition, 'id');
        if ($info) {
            throw new Exception('第' . $row . '行' . '商品名称已存在');
        }
        $commodity_data['commodity_name'] = $commodity_name;



        $specification = $data['specification']? : '';
        $specification_len = mb_strlen($specification, 'utf8');
        if ($specification_len > 30) {
            throw new Exception('第' . $row . '行' . '规格不能超过30个字符');
        }
        $commodity_data['specification'] = $specification;

        $commodity_code = $data['commodity_code']? : '';
        $commodity_code_len = mb_strlen($commodity_code, 'utf8');
        if ($commodity_code_len > 30) {
            throw new Exception('第' . $row . '行' . '商品编码不能超过30个字符');
        }
        $commodity_data['commodity_code'] = $commodity_code;

        $classification_name = $data['classification_name']? : '';
        if ($classification_name) {
            $claasification_condition['classification_name'] = $classification_name;
            $claasification_condition['status'] = 1;
            $claasification_condition['store_id'] = $store_id;
            $claasification_condition['depth'] = 3;
            $claasification_info = classificationInsert::getField($claasification_condition, 'id');
            if (!$claasification_info) {
                throw new Exception('第' . $row . '行' . '商品分类参数有误');
            }
            $commodity_data['classification_name'] = $classification_name;
            $commodity_data['classification_id'] = $claasification_info['id'];
        } else {
            throw new Exception('第' . $row . '行' . '商品分类不能为空');
        }

        $commodity_data['price'] = array_key_exists('price', $data) ? round($data['price'], 2) : 0.00;


        $barcode = $data['barcode']? : '';
        $barcode_len = mb_strlen($barcode, 'utf8');
        if ($barcode_len > 30) {
            throw new Exception('第' . $row . '行' . '条形码不能超过30个字符');
        }
        $commodity_data['barcode'] = $barcode;

        $unit_name = $data['unit_name']? : '';
        if ($unit_name) {
            $unit_condition['unit_name'] = $unit_name;
            $unit_condition['status'] = 1;
            $unit_condition['store_id'] = $store_id;
            $unit_info = unitInsert::getField($unit_condition, 'id');
            if (!$unit_info) {
                throw new Exception('第' . $row . '行' . '商品单位参数有误');
            }
            $commodity_data['unit_name'] = $unit_name;
            $commodity_data['unit_id'] = $unit_info['id'];
        } else {
            throw new Exception('第' . $row . '行' . '商品单位不能为空');
        }

        $commodity_property_name = $data['commodity_property_name']? : '';
        $commodity_property_name_len = mb_strlen($barcode, 'utf8');
        if ($commodity_property_name_len > 30) {
            throw new Exception('第' . $row . '行' . '配件属性不能超过30个字符');
        }
        $commodity_data['commodity_property_name'] = $commodity_property_name;

        $comment = $data['comment']? : '';
        $comment_len = mb_strlen($comment, 'utf8');
        if ($comment_len > 200) {
            throw new Exception('第' . $row . '行' . '商品备注不能超过200个字符(1个中文或1个英文相当于1个字符)');
        }
        $commodity_data['comment'] = $comment;

        $commodity_originate_name = $data['commodity_originate_name']? : '';
        if ($commodity_originate_name) {
            $commodity_originate_condition['name'] = $commodity_originate_name;
            $commodity_originate_info = getCommodityOriginate::getField($commodity_originate_condition, 'id');
            if (!$commodity_originate_info) {
                throw new Exception('第' . $row . '行' . '来源不存在');
            }
            $commodity_originate_name = $commodity_originate_info['id'];
        } else {
            $commodity_originate_name = 1;
        }
        $commodity_data['commodity_originate_id'] = $commodity_originate_name;


        $default_depot_name = $data['default_depot_name']? : NULL;
        if ($default_depot_name) {
            $depot_condition['depot_name'] = $default_depot_name;
            $depot_condition['status'] = 1;
            $depot_condition['store_id'] = $store_id;
            $depot_info = getDepot::getField($depot_condition, 'id');
            if (!$depot_info) {
                throw new Exception('第' . $row . '行' . '仓库不存在');
            }
            $default_depot_name = $depot_info['id'];
        }
        $commodity_data['default_depot_id'] = $default_depot_name;


        $commodity_data['last_modified_by'] = current($userIdentity)['id'];
        $commodity_data['last_modified_time'] = date('Y-m-d H:i:s');

        $commodity_data['created_time'] = date('Y-m-d H:i:s');
        $commodity_data['created_by'] = current($userIdentity)['id'];
        $commodity_data['store_id'] = $store_id;
        return $commodity_data;
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

        $condition['import_number'] = $import_number = rand(pow(10, $len - 1), pow(10, $len) - 1);

        $info = Insert::getField($condition, 'import_number');
        if ($info) {
            $import_number = self::getEmployeeNumber($len, $condition);
        }

        return (string) $import_number;
    }

}
