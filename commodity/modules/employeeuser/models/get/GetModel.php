<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employeeuser\models\get;

use common\models\Model as CommonModel;
use commodity\modules\employeeuser\models\get\db\Select;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';
    const ACTION_GETPART = 'action_getpart';

    public $token;
    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $store_id;
    public $status;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id', 'token'],
            self::ACTION_GETPART => ['count_per_page', 'page_num', 'keyword', 'status', 'store_id', 'token'],
            self::ACTION_GETALL => ['token', 'status'],
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
            ['status', 'default', 'value' => 1],
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

    public function actionGetpart() {
        try {
            
            $keyword = $this->keyword;
            $store_id = current(Select::getUser())->store_id;
            $status = $this->status;
            $condition = array();
            $i = 0;
            $condition[0] = 'and';

            $condition_like = [ 'or', ['like', 'name', $keyword], ['like', 'account_name', $keyword]];
            if ($keyword) {
                $i++;
                $condition[$i] = $condition_like;
            }

            $i++;
            $condition[$i] = 'store_id=' . $store_id;

            $i++;
            $condition[$i] = 'status=' . $status;

            $i++;
            $condition[$i] = 'orignate=0';
            
            $i++;
            $condition[$i] = 'id!=' . current(Select::getUser())->id;
            
            $result = Select::getpart($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getpart', 2005);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'employeeuser' => $result->models,
        ];
    }

    public function actionGetall() {
        try {
            $condition['a.store_id'] = current(Select::getUser())->store_id;
            $condition['a.status'] = $this->status;
            $condition['a.orignate'] = 0;
            return Select::getall($condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }
    }

}
