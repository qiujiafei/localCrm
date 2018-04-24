<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\get;

use commodity\modules\bill\models\get\db\SelectModel;
use commodity\modules\bill\models\get\db\GetBillService;
use commodity\modules\bill\models\get\db\GetBillPicking;
use commodity\modules\finance\models\get\db\CustomerInformationSelectModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use common\components\handler\ExcelHandler;

class GetModel extends CommonModel {

    const ACTION_LISTS = 'action_lists';
    const ACTION_GETONE = 'action_getone';
    const ACTION_GETNOACCOUNT = 'action_getNoAccount';
    const ACTION_GETACCOUNT = 'action_getAccount';
    const ACTION_GETEXPORT = 'action_getexport';
    const ACTION_GETBILLMEBERS = 'action_getbillmembers';
    //客户统计接口
    const ACTION_CUSTOMER = 'action_customer';

    public $page;
    public $pageSize;
    public $startTime;
    public $endTime;
    public $service;
    public $builderBy;  //施工人员
    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $status;
    public $end_time;
    public $start_time;
    public $type;

    public function scenarios() {
        return [
            self::ACTION_LISTS => [],
            self::ACTION_GETONE => ['id'],
            self::ACTION_GETNOACCOUNT => ['count_per_page', 'page_num', 'keyword', 'status', 'start_time', 'end_time'],
            self::ACTION_GETACCOUNT => ['count_per_page', 'page_num', 'keyword', 'status', 'start_time', 'end_time'],
            self::ACTION_GETEXPORT => ['keyword', 'status', 'start_time', 'end_time'],
            self::ACTION_GETBILLMEBERS => ['type', 'start_time', 'end_time'],
            self::ACTION_CUSTOMER => ['type', 'start_time', 'end_time'],
        ];
    }

    public function rules() {
        return [
            [
                ['id', 'token'],
                'required',
                'message' => 2004,
            ],
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
        ];
    }

    /**
     * 待保留
     * 获取列表，车系未完成
     * @return array
     */
    public function actionLists() {
        $where = [];
        $lists = SelectModel::findLists($where, $this->pageSize);
        $tempData = [];

        if (count($lists['lists'])) {
            foreach ($lists['lists'] as $key => $model) {
                $tempData = $model->toArray();
                $customerInfo = $model->customerInformation;
                $customerCars = $model->customerCars;
                //客户姓名
                $tempData['customer_name'] = $customerInfo['customer_name'];
                //客户手机号
                $tempData['cellphone_number'] = $customerInfo['cellphone_number'];

                //车架号
                $tempData['frame_number'] = $customerCars['frame_number'];
                //车牌号
                $tempData['number_plate_number'] = $customerCars['number_plate_number'];

                $tempData['service_and_commodity'] = $model->getMergeServiceAndCommodity($model->service, $model->getCommodityNames($model->id));

                $lists['lists'][$key] = $tempData;
            }
        }
        return $lists;
    }

    public function actionGetone() {
        try {

            $condition['a.store_id'] = current(SelectModel::getUser())->store_id;
            $condition['a.id'] = $this->id;

            //开单客户资料
            $result['customer_infomation'] = SelectModel::getone($condition);

            if (!$result) {
                throw new \Exception('无法获取-1', 2005);
                return false;
            }
            //服务项目
            print_r($result);
            die;
            //商品

            return $result;
        } catch (\Exception $ex) {
            $this->addError('getone', 2005);
            return false;
        }

        return $result;
    }

    //未结算列表
    public function actionGetNoAccount() {
        try {
            $start_time = $this->start_time;
            $end_time = $this->end_time;
            if ($start_time) {
                $v_start_time = strtotime($start_time);
                if (!$v_start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
                if ($end_time) {
                    $v_end_time = strtotime($end_time);
                    if (!$v_end_time) {
                        throw new \Exception('结束时间格式有问题', 17055);
                    }
                    if ($v_start_time >= $v_end_time) {
                        throw new \Exception('结束时间必须大于开始时间', 17056);
                    }
                }
            }


            $keyword = $this->keyword;
            $store_id = current(SelectModel::getUser())->store_id;
            $status = 0;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($keyword) {
                $condition_like = ['or', ['like', 'a.bill_number', $keyword], ['like', 'b.customer_name', $keyword]];
                $i++;
                $condition[$i] = $condition_like;
            }

            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                }
                $i++;
                $condition[$i] = $time;
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'a.status=' . $status;
            $result = SelectModel::getnoall($this->count_per_page, $this->page_num, $condition);
            $bill_result = $result->models;

            foreach ($bill_result as $key => $value) {
                $service_condition['a.bill_id'] = $value['id'];
                $service_name_result = GetBillService::BillServiceName($service_condition);
                foreach ($service_name_result as $service_key => $service_name) {
                    $bill_result[$key]['service_name'][$service_key] = $service_name['service_name'];
                }
                $picking_commodity_condition['d.id'] = $value['id'];
                $bill_picking_commodity = GetBillPicking::BillPickingCommodityName($picking_commodity_condition);
                foreach ($bill_picking_commodity as $commodity_key => $commodity_name) {
                    $bill_result[$key]['picking_commodity_name'][$commodity_key] = $commodity_name['commodity_name'];
                }
            }
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17054) {
                $this->addError('getall', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getall', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getall', 17056);
                return false;
            } else {
                $this->addError('getall', 2005);
                return false;
            }
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'getnoaccount' => $bill_result,
        ];
    }

    //结算列表
    public function actionGetAccount() {
        try {
            $start_time = $this->start_time;
            $end_time = $this->end_time;
            if ($start_time) {
                $v_start_time = strtotime($start_time);
                if (!$v_start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
                if ($end_time) {
                    $v_end_time = strtotime($end_time);
                    if (!$v_end_time) {
                        throw new \Exception('结束时间格式有问题', 17055);
                    }
                    if ($v_start_time >= $v_end_time) {
                        throw new \Exception('结束时间必须大于开始时间', 17056);
                    }
                }
            }


            $keyword = $this->keyword;
            $store_id = current(SelectModel::getUser())->store_id;
            $status = 1;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($keyword) {
                $condition_like = ['or', ['like', 'a.bill_number', $keyword], ['like', 'b.customer_name', $keyword]];
                $i++;
                $condition[$i] = $condition_like;
            }

            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                }
                $i++;
                $condition[$i] = $time;
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'a.status=' . $status;
            $result = SelectModel::getall($this->count_per_page, $this->page_num, $condition);
            $bill_result = $result->models;

            foreach ($bill_result as $key => $value) {
                $service_condition['a.bill_id'] = $value['id'];
                $service_name_result = GetBillService::BillServiceName($service_condition);
                foreach ($service_name_result as $service_key => $service_name) {
                    $bill_result[$key]['service_name'][$service_key] = $service_name['service_name'];
                }
            }
        } catch (\Exception $ex) {
           
            if ($ex->getCode() === 17054) {
                $this->addError('getall', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getall', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getall', 17056);
                return false;
            } else {
                $this->addError('getall', 2005);
                return false;
            }
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'getaccount' => $bill_result,
        ];
    }

    //结算导出
    public function actionGetExport() {
        try {

            $start_time = $this->start_time;
            $end_time = $this->end_time;
            if ($start_time) {
                $v_start_time = strtotime($start_time);
                if (!$v_start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
                if ($end_time) {
                    $v_end_time = strtotime($end_time);
                    if (!$v_end_time) {
                        throw new \Exception('结束时间格式有问题', 17055);
                    }
                    if ($v_start_time >= $v_end_time) {
                        throw new \Exception('结束时间必须大于开始时间', 17056);
                    }
                }
            }


            $keyword = $this->keyword;
            $store_id = current(SelectModel::getUser())->store_id;
            $status = 1;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($keyword) {
                $condition_like = ['or', ['like', 'a.bill_number', $keyword], ['like', 'b.customer_name', $keyword]];
                $i++;
                $condition[$i] = $condition_like;
            }

            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                }
                $i++;
                $condition[$i] = $time;
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            if ($status >= 0) {
                $i++;
                $condition[$i] = 'a.status=' . $status;
            }

            $title = [
                '开单号',
                '车架号',
                '车牌号',
                '客户姓名',
                '车系',
                '手机号',
//                '服务项目 ',
                '是否会员',
                '优惠金额',
                '施工金额',
                '支付金额',
                '施工人员',
                '创建时间',
                '结算时间',
                '备注',
                '结算人',
            ];

            $result = SelectModel::getexport($condition);

            $new_result = array();
            foreach ($result as $key => $value) {
                $new_result[$key]['bill_number'] = $value['bill_number'];
                $new_result[$key]['frame_number'] = $value['frame_number'];
                $new_result[$key]['car_number'] = $value['number_plate_province_name'] . $value['number_plate_alphabet_name'] . $value['number_plate_number'];
                $new_result[$key]['customer_name'] = $value['customer_name'];
                $new_result[$key]['car_type'] = $value['brand_name'] . $value['alphabet_name'] . '' . $value['year'] . '款' . $value['style_name'];
                $new_result[$key]['cellphone_number'] = $value['cellphone_number'];
                if ($value['is_member'] == 0) {
                    $value['is_member'] = '否';
                } elseif ($value['is_member'] == 1) {
                    $value['is_member'] = '是';
                }
                $new_result[$key]['is_member'] = $value['is_member'];
                $new_result[$key]['member_discount'] = $value['member_discount'];
                $new_result[$key]['price'] = $value['price'];
                $new_result[$key]['final_price'] = $value['final_price'];
                $new_result[$key]['technician_name'] = $value['technician_name'];
                $new_result[$key]['created_time'] = $value['created_time'];
                $new_result[$key]['comment'] = $value['comment'];
                $new_result[$key]['last_modified_name'] = $value['last_modified_name'];
            }

            ExcelHandler::output($new_result, $title, date('Y_m_d') . '单据资料');

            return [];
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17054) {
                $this->addError('getexport', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getexport', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getexport', 17056);
                return false;
            } else {
                $this->addError('getexport', 19008);
                return false;
            }
        }
    }

    /**
     * 开单会员统计
     * $type 1.日  2.月  3.年 默认总数
     */
    public function actionGetbillmembers() {
        try {

            $store_id = current(SelectModel::getUser())->store_id;
            $type = $this->type;
            $start_time = $this->start_time;
            $end_time = $this->end_time;
            if ($start_time) {
                $v_start_time = strtotime($start_time);
                if (!$v_start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
                if ($end_time) {
                    $v_end_time = strtotime($end_time);
                    if (!$v_end_time) {
                        throw new \Exception('结束时间格式有问题', 17055);
                    }
                    if ($v_start_time >= $v_end_time) {
                        throw new \Exception('结束时间必须大于开始时间', 17056);
                    }
                }
            }

            if ($type == 1) {
                //日
                $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
                $end = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            } elseif ($type == 2) {
                //月
                $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
                $end = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));
            } elseif ($type == 3) {
                //年
                $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, date('Y')));
                $end = date('Y-m-d H:i:s', mktime(23, 59, 59, 12, 31, date('Y')));
            }

            $i = 0;
            $condition[0] = 'and';
            if ($type) {
                $condition = [ 'and', ['>=', 'a.created_time', $begin], ['<', 'a.created_time', $end]];
                $i++;
                $condition[$i] = $condition;
            }
            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                }
                $condition[$i] = $time;
            }

            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'b.is_member=0';
            $result['traveler_count'] = SelectModel::getcount($condition)? : 0;

            $condition[$i] = 'b.is_member=1';
            $result['member_count'] = SelectModel::getcount($condition)? : 0;
            return $result;
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17054) {
                $this->addError('getbillmembers', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getbillmembers', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getbillmembers', 17056);
                return false;
            } else {
                $this->addError('getbillmembers', 2005);
                return false;
            }
        }
    }

    public function actionCustomer()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $where = [
            'and',
            ['store_id' => $storeId],
            //只查询正常的用户
            ['status' => 1]
        ];
        $model = new CustomerInformationSelectModel();
        $query = $model->getStatisticsOfMonthQuery($where);
        $member = $notMember = 0;
        //循环数据进行统计计算
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                $data = $model->toArray();
                //会员
                $member += $data['is_member'] ? 1 : 0;
                //非会员
                $notMember += $data['is_member'] ? 0 : 1;

            }
        }
        $memberInfo['member_count'] = $member;
        $memberInfo['traveler_count'] = $notMember;
        return $memberInfo;
    }
}
