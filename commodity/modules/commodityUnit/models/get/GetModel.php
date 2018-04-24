<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commodityUnit\models\get;

use common\models\Model as CommonModel;
use commodity\modules\commodityUnit\models\get\db\Select;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';

    public $token;
    public $id;
    public $count_per_page;
    public $page_num;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id', 'token'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'token'],
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

            $result = Select::getone($this->id);
             if(!$result){
                throw new \Exception('无法获取-1', 2005);
                return false;
            }
            return $result;
        } catch (\Exception $ex) {

            $this->addError('getone', 2005);

            return false;
        }
    }

    public function actionGetall() {
        try {

            $condition['store_id'] = current(Select::getUser())->store_id;
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 3008);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'unit' => $result->models,
        ];
    }

}
