<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\serviceaddition\models\get;

use common\models\Model as CommonModel;
use commodity\modules\serviceaddition\models\get\db\Select;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';

    public $token;
    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id', 'token'],
            self::ACTION_GETALL => ['keyword', 'count_per_page', 'page_num', 'token'],
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
                [ 'keyword'],
                'string',
                'length' => [1, 30],
                'tooShort' => 9001,
                'tooLong' => 9002,
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

        $condition = array();
        try {
            $keyword = $this->keyword;
            if ($keyword) {
                $condition['addition_name'] = ['like', 'addition_name', $keyword];
            }
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {

            $this->addError('getall', 9033);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'service_addition' => $result->models,
        ];
    }

}
