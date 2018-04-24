<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damagedcommodity\models\get;

use common\models\Model as CommonModel;
use commodity\modules\damagedcommodity\models\get\db\Select;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETALL = 'action_getall';

    public $count_per_page;
    public $page_num;
    public $number;
    public $commodity_name;
    public $status;

    public function scenarios() {
        return [
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'number', 'commodity_name', 'status'],
        ];
    }

    public function rules() {
        return [
            [
                [ 'damaged_id', 'token'],
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

    /**
     * 报损明细列表
     */
    public function actionGetall() {
        try {
            $number = $this->number;
            $commodity_name = $this->commodity_name;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($number) {
                $i++;
                $condition[$i] = ['like', 'b.number', $number];
            }
            if ($commodity_name) {
                $i++;
                $condition[$i] = ['like', 'c.commodity_name', $commodity_name];
            }
            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $i++;
            $condition[$i] = 'b.status=' . $status;
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'damaged_commodity' => $result->models,
        ];
    }

}
