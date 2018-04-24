<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\models\get;

use common\models\Model as CommonModel;
use commodity\modules\customercarstirebrand\models\get\db\Select;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';

    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $store_id;
    public $status;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id'],
            self::ACTION_GETALL => [],
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
        ];
    }

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

    public function actionGetall() {
        try {
            $condition['store_id'] = current(Select::getUser())->store_id;
            $condition['status'] = 1;
            $field = 'id,brand_name';
            return Select::getall($condition, $field);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }
    }

}
