<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\damageddestroy\models\get;

use common\models\Model as CommonModel;
use commodity\modules\damageddestroy\models\get\db\Select;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';

    public $count_per_page;
    public $page_num;
    public $damaged_number;
    public $store_id;
    public $status;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['damaged_number'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'damaged_number', 'status'],
        ];
    }

    public function rules() {
        return [
            [
                ['damageddestroy_number', 'token'],
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
            $result = Select::getone($this->damageddestroy_number);
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

    //作废单据列表
    public function actionGetall() {
        try {

            $damaged_number = $this->damaged_number;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            
            if ($damaged_number) {
                $i++;
                $condition[$i] = ['like', 'a.damaged_number', $damaged_number];
            }

            $i++;
            $condition[$i] = 'a.store_id=' . $store_id;
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'damageddestroy' => $result->models,
        ];
    }

}
