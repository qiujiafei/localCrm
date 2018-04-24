<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damaged\models\get;

use common\models\Model as CommonModel;
use commodity\modules\damaged\models\get\db\Select;
use commodity\modules\damagedcommodity\models\get\db\Select as damagedcommodity_select;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';
    const ACTION_GETMONEY = 'action_getmoney';

    public $count_per_page;
    public $page_num;
    public $id;
    public $number;
    public $store_id;
    public $status;
    public $statistical_type; //1.一天  2.本月   3.累计总的

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'number', 'status'],
            self::ACTION_GETMONEY => ['statistical_type', 'status'],
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
            ['status', 'default', 'value' => 0],
            ['statistical_type', 'default', 'value' => 0],
        ];
    }

    //报损商品详情
    public function actionGetone() {
        try {
            $result = Select::getone($this->id);

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
     * 报损单据
     */
    public function actionGetall() {
        try {

            $number = $this->number;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $condition = array();
            $i = 0;
            if ($number) {
                $condition_like = ['like', 'a.number', $number];
                $condition[0] = 'and';
                $i++;
                $condition[$i] = $condition_like;
                $i++;
                $condition[$i] = 'a.store_id=' . $store_id;
                $i++;
                $condition[$i] = 'a.status=' . $status;
            } else {
                $condition['a.store_id'] = $store_id;
                $condition['a.status'] = $status;
            }
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }
        $damaged = $result->models;
        foreach ($damaged as $key => $value) {

            $damaged_condition['damaged_id'] = $value['id'];
            $field = 'quantity,total_price';
            $damaged_data = damagedcommodity_select::getProAll($damaged_condition, $field);
            $quantity = 0;
            $total_price = 0;
            foreach ($damaged_data as $value) {
                $quantity+=$value['quantity'];
                $total_price+=$value['total_price'];
            }
            $damaged[$key]['quantity'] = $quantity;
            $damaged[$key]['total_price'] = $total_price;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'damaged' => $damaged,
        ];
    }

    /**
     * 报损统计
     * 
     * $statistical_type   1.一天  2.本月   3.累计总的
     */
    public function actionGetmoney() {
        try {
            $statistical_type = $this->statistical_type;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $total_condition['a.store_id'] = $store_id;
            $total_condition['a.status'] = $status;
            $result['total_money'] = Select::getSumMoney('b.total_price', $total_condition)->query? : 0;
            
            $i = 0;
            //日
            $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $end = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $time= [ 'and', ['>=', 'b.created_time', $begin], ['<', 'b.created_time', $end]];
            $condition[0] = 'and';
            $i++;
            $condition[$i] = $time;
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'a.status=' . $status;
            $result['day_money'] = Select::getSumMoney('b.total_price', $condition)->query? : 0;
       
            //月
            $begin = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
            $end = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));
            $time = [ 'and', ['>=', 'b.created_time', $begin], ['<', 'b.created_time', $end]];
            $condition[1] = $time;
            $result['month_money'] = Select::getSumMoney('b.total_price', $condition)->query? : 0;

            return $result;
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }
    }

}
