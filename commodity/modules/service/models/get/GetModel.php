<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\service\models\get;

use common\models\Model as CommonModel;
use commodity\modules\service\models\get\db\Select;
use common\components\handler\ExcelHandler;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';
    const ACTION_GETEXPORT = 'action_getexport';

    public $token;
    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $status;
    public $service_claasification_id;
    public $service_name;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id', 'token'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'keyword', 'service_claasification_id', 'status', 'token', 'service_name'],
            self::ACTION_GETEXPORT => [ 'keyword', 'service_claasification_id', 'status', 'token'],
        ];
    }

    public function rules() {
        return [
            [
                ['id', 'token'],
                'required',
                'message' => 2004,
            ],
            ['status', 'default', 'value' => 1],
            [
                ['count_per_page', 'page_num', 'service_claasification_id'],
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
            if (!$result) {
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

            $keyword = $this->keyword;
            $status = $this->status;
            $service_claasification_id = $this->service_claasification_id;

            $condition_like=$condition = array();
            $i = 0;
            if ($this->service_name) {
                $condition_like = ['like', 'service_name',  $this->service_name];
            } elseif ($this->keyword)  {
                $condition_like = [ 'or', ['like', 'service_name', $keyword], ['like', 'specification', $keyword], ['like', 'service_code', $keyword]];
            }

            $condition[0] = 'and';
            if ($condition_like) {
                $i++;
                $condition[$i] = $condition_like;
            }
            $i++;
            $condition[$i] = 'status=' . $status;

            if ($service_claasification_id) {
                $i++;
                $condition[$i] = 'service_claasification_id=' . $service_claasification_id;
            }
            $i++;
            $condition[$i] = 'store_id=' . current(Select::getUser())->store_id;

            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2006);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'service' => $result->models,
        ];
    }

    public function actionGetExport() {
        try {

            $keyword = $this->keyword;
            $status = $this->status;
            $service_claasification_id = $this->service_claasification_id;

            $condition = array();
            $i = 0;
            $condition_like = [ 'or', ['like', 'service_name', $keyword], ['like', 'specification', $keyword], ['like', 'service_code', $keyword]];
            $condition[0] = 'and';
            if ($keyword) {
                $i++;
                $condition[$i] = $condition_like;
            }
            $i++;
            $condition[$i] = 'status=' . $status;

            if ($service_claasification_id) {
                $i++;
                $condition[$i] = 'service_claasification_id=' . $service_claasification_id;
            }
            $i++;
            $condition[$i] = 'store_id=' . current(Select::getUser())->store_id;

            $title = [
                '服务项目名称',
                '规格',
                '编码',
                '售价',
                '类型',
                '门店ID',
                '服务项目分类名称 ',
                '状态',
                '备注',
                '创建者ID',
                '创建时间',
            ];

            $result = Select::getexport($condition);

            ExcelHandler::output($result, $title, date('Y_m_d') . '服务项目');

            return [];
        } catch (\Exception $ex) {
            $this->addError('getexport', 9036);
            return false;
        }
    }

}
