<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\models\get;

use common\models\Model as CommonModel;
use commodity\modules\employee\models\get\db\Select;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';
    const ACTION_GETEMPLOYEE = 'action_getemployee';

    public $count_per_page;
    public $page_num;
    public $employee_number;
    public $keyword;
    public $store_id;
    public $status;
    public $employee_type_name;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['employee_number'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'keyword', 'status', 'store_id'],
            self::ACTION_GETEMPLOYEE => [],
        ];
    }

    public function rules() {
        return [
            [
                ['employee_number', 'token'],
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
        ];
    }

    public function actionGetone() {
        try {
            $result = Select::getone($this->employee_number);
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

    public function actionGetall() {
        try {

            $keyword = $this->keyword;
            $store_id =current(Select::getUser())->store_id;
            $status = $this->status;
            $condition = array();
            $i = 0;
            if ($keyword) {

                $condition_like = ['like', 'name', $keyword];

                if ($store_id) {
                    $condition[0] = 'and';
                    $i++;
                    $condition[$i] = $condition_like;
                } else {
                    $condition = $condition_like;
                }

                if ($store_id) {
                    $i++;
                    $condition[$i] = 'store_id=' . $store_id;
                }
                $i++;
                $condition[$i] = 'status=' . $status;
            } else {
                if ($store_id) {
                    $condition['store_id'] = $store_id;
                }
                $condition['status'] = $status;
            }
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'employee' => $result->models,
        ];
    }

    public function actionGetemployee() {
        try {
            $condition['store_id'] = current(Select::getUser())->store_id;
            $condition['status'] = 0;
            $field = 'id,name';
            return Select::getemployee($condition, $field);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }
    }

}
