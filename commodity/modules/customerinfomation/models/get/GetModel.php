<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\models\get;

use common\models\Model as CommonModel;
use commodity\modules\customerinfomation\models\get\db\Select;
use commodity\modules\customerinfomation\models\get\db\GetCarsInfo;
use common\components\handler\ExcelHandler;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';
    const ACTION_GETEXPORT = 'action_getexport';
    const ACTION_GETMEMBERSTATISTICS = 'action_getmemberstatistics';
    const ACTION_GETALLMEMBER = 'action_getallmember';
    const ACTION_GETONEMEMBER = 'action_getonemember';

    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $store_id;
    public $status;
    public $customerinfomation_type_name;
    public $type;
    public $end_time;
    public $start_time;
    public $is_member;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'keyword', 'status', 'status', 'is_member'],
            self::ACTION_GETEXPORT => [ 'keyword', 'status', 'status', 'is_member'],
            self::ACTION_GETMEMBERSTATISTICS => ['type', 'start_time', 'end_time'],
            self::ACTION_GETALLMEMBER => ['keyword', 'start_time', 'end_time', 'count_per_page', 'page_num'],
            self::ACTION_GETONEMEMBER => ['id'],
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

    public function actionGetone() {
        try {
            $result['customer_info'] = Select::getone($this->id);

            if (!$result) {
                throw new \Exception('无法获取-1', 2005);
                return false;
            }
            $condition['a.store_id'] = current(Select::getUser())->store_id;
            $condition['a.customer_infomation_id'] = $this->id;
            $result['car_info'] = GetCarsInfo::getCarsCustomerInfomation($condition);
            return $result;
        } catch (\Exception $ex) {
            $this->addError('getone', 2005);
            return false;
        }

        return $result;
    }

    public function actionGetall() {
        try {

            $keyword = $this->keyword;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $is_member = $this->is_member;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($keyword) {
                $condition_like = ['or', ['like', 'a.customer_name', $keyword], ['like', 'a.cellphone_number', $keyword], ['like', 'b.plate_number', $keyword], ['like', 'b.frame_number', $keyword]];
                $i++;
                $condition[$i] = $condition_like;
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            if ($status != NULL) {
                $i++;
                $condition[$i] = 'a.status=' . $status;
            } else {
                $i++;
                $condition[$i] = 'a.status=1';
            }
            if ($is_member != NULL) {
                $i++;
                $condition[$i] = 'a.is_member=' . $is_member;
            }

            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'customerinfomation' => $result->models,
        ];
    }

    //客户导出
    public function actionGetExport() {
        try {

            $keyword = $this->keyword;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $is_member = $this->is_member;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($keyword) {
                $condition_like = ['or', ['like', 'a.customer_name', $keyword], ['like', 'a.cellphone_number', $keyword], ['like', 'b.plate_number', $keyword], ['like', 'b.frame_number', $keyword]];
                $i++;
                $condition[$i] = $condition_like;
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            if ($status != NULL) {
                $i++;
                $condition[$i] = 'a.status=' . $status;
            } else {
                $i++;
                $condition[$i] = 'a.status=1';
            }
            if ($is_member != NULL) {
                $i++;
                $condition[$i] = 'a.is_member=' . $is_member;
            }

            $title = [
                '顾客姓名',
                '性别',
                '手机',
                '客户来源',
                '消费次数',
                '累计消费',
                '车牌 ',
                '车架号',
                '品牌车型',
                '保险到期',
                '是否会员',
                '创办人',
                '创建时间',
                '备注信息',
            ];

            $result = Select::getexport($condition);

            $new_result = array();
            foreach ($result as $key => $value) {
                $new_result[$key]['customer_name'] = $value['customer_name'];
                if ($value['gender'] == 0) {
                    $value['gender'] = '女';
                } elseif ($value['gender'] == 1) {
                    $value['gender'] = '男';
                } else {
                    $value['gender'] = '其他';
                }
                $new_result[$key]['gender'] = $value['gender'];
                $new_result[$key]['cellphone_number'] = $value['cellphone_number'];
                $new_result[$key]['customer_origination'] = $value['customer_origination'];
                $new_result[$key]['consume_count'] = $value['consume_count'];
                $new_result[$key]['total_consume_price'] = $value['total_consume_price'];
                $new_result[$key]['car_number'] = $value['number_plate_province_name'] . $value['number_plate_alphabet_name'] . $value['number_plate_number'];
                $new_result[$key]['frame_number'] = $value['frame_number'];
                $new_result[$key]['car_type'] = $value['brand_name'] . $value['alphabet_name'] . '' . $value['year'] . '款' . $value['style_name'];
                $new_result[$key]['insurance_expire'] = $value['insurance_expire'];
                if ($value['is_member'] == 0) {
                    $value['is_member'] = '否';
                } elseif ($value['is_member'] == 1) {
                    $value['is_member'] = '是';
                }
                $new_result[$key]['is_member'] = $value['is_member'];
                $new_result[$key]['created_name'] = $value['created_name'];
                $new_result[$key]['created_time'] = $value['created_time'];
                $new_result[$key]['comment'] = $value['comment'];
            }

            ExcelHandler::output($new_result, $title, date('Y_m_d') . '客户资料');

            return [];
        } catch (\Exception $ex) {
            $this->addError('getexport', 9036);
            return false;
        }
    }

    public function actionGetonemember() {
        try {
            $result = Select::getonemember($this->id);

            if (!$result) {
                throw new \Exception('无法获取-1', 2005);
                return false;
            }
            return $result;
        } catch (\Exception $ex) {
            $this->addError('getone', 2005);
            return false;
        }

        return $result;
    }

    /**
     * 会员列表
     */
    public function actionGetallmember() {
        try {

            $keyword = $this->keyword;
            $store_id =current(Select::getUser())->store_id;
            $status = 1;
            $is_member = 1;
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

            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'h.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'h.created_time', $start_time], ['<', 'h.created_time', $end_time]];
                }
                $i++;
                $condition[$i] = $time;
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'a.status=' . $status;
            $i++;
            $condition[$i] = 'a.is_member=' . $is_member;

            $result = Select::getallmember($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            if ($ex->getCode() === 3006) {
                $this->addError('getallmember', 3006);
                return false;
            } elseif ($ex->getCode() === 17054) {
                $this->addError('getallmember', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getallmember', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getallmember', 17056);
                return false;
            } else {
                $this->addError('getallmember', 2005);
                return false;
            }
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'customerinfomation' => $result->models,
        ];
    }

    /**
     * 统计会员
     * $type 1.日  2.月  3.年 默认总数
     */
    public function actionGetmemberstatistics() {
        try {

            $store_id = current(Select::getUser())->store_id;
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
            $condition_member[0] = $condition[0] = 'and';
            $i++;
            if ($type && !$start_time) {
                $condition = [ 'and', ['>=', 'a.created_time', $begin], ['<', 'a.created_time', $end]];
                $condition_member[$i] = $condition[$i] = $condition;
            } else {
                if ($start_time) {
                    $start_time = date('Y-m-d H:i:s', $v_start_time);
                    $time = ['>=', 'a.created_time', $start_time];
                    if ($end_time) {
                        $end_time = date('Y-m-d H:i:s', $v_end_time);
                        $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                    }
                    $condition_member[$i] = $condition[$i] = $time;
                }
            }

            $i++;
            $condition_member[$i] = 'b.store_id=' . $store_id;
            $i++;
            $condition_member[$i] = 'b.status=1';
            $i++;
            $condition_member[$i] = 'b.is_member=1';
            $result_member = Select::getmembercount($condition_member);
            $result_all['member_count'] = $result_member->query? : 0;
            
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'a.status=1';
            $i++;
            $condition[$i] = 'a.is_member=0';
            $result = Select::getcount($condition);
            $result_all['traveler_count'] = $result->query? : 0;

            return $result_all;
            
        } catch (\Exception $ex) {
        
            if ($ex->getCode() === 17054) {
                $this->addError('getmemberstatistics', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getmemberstatistics', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getmemberstatistics', 17056);
                return false;
            } else {
                $this->addError('getmemberstatistics', 2005);
                return false;
            }
        }
    }

}
