<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get;

use common\models\Model as CommonModel;
use commodity\modules\commodityManage\models\get\db\Insert;
use commodity\modules\commodityManage\models\get\db\Select;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETALL = 'action_getall';
    const ACTION_GETEXPORT = 'action_getexport';

    public $count_per_page;
    public $page_num;
    public $commodity_name;
    public $barcode;
    public $keyword;
    public $classification_name;
    public $status;
    public $store_id;

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['commodity_name', 'barcode'],
            self::ACTION_GETALL => ['count_per_page', 'page_num', 'keyword', 'classification_name', 'status', 'store_id'],
            self::ACTION_GETEXPORT => ['keyword', 'classification_name', 'status', 'store_id'],
        ];
    }

    public function rules() {
        return [
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
            ['status', 'in', 'range' => [0, 1], 'message' => 2004],
            [['status', 'store_id'], 'integer', 'message' => 2004],
        ];
    }

    public function actionGetone() {

        try {
            $result = Select::getone($this->commodity_name, $this->barcode);
        } catch (\Exception $ex) {
            $this->addError('getone', 2005);
            return false;
        }

        if (!$result) {
            $this->addError('getone', 2006);
            return false;
        }

        return $result;
    }

    public function actionGetall() {
        try {

            $keyword = $this->keyword;
            $classification_name = $this->classification_name;
            $status = $this->status;

            $condition = array();
            $i = 0;
            $condition_like = [ 'or', ['like', 'commodity.commodity_name', $keyword], ['like', 'commodity.barcode', $keyword]];
            $condition[0] = 'and';
            if ($keyword) {
                $i++;
                $condition[$i] = $condition_like;
            }


            if ($classification_name) {
                $i++;
                $condition[$i] = 'commodity.classification_name=' . "'" . $classification_name . "'";
            }

            if ($status!=NULL) {
                $i++;
                $condition[$i] = 'commodity.status=' . $status;
            }
            $i++;
            $condition[$i] = 'commodity.store_id=' . AccessTokenAuthentication::getUser(true);
           
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {

            $this->addError('getall', 2006);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'commodity' => $result->models,
        ];
    }

    public function actionGetExport() {
        try {

            $keyword = $this->keyword;
            $classification_name = $this->classification_name;
            $status = $this->status;

            $condition = array();
            $i = 0;
            $condition_like = [ 'or', ['like', 'commodity_name', $keyword], ['like', 'specification', $keyword]];
            $condition[0] = 'and';
            if ($keyword) {
                $i++;
                $condition[$i] = $condition_like;
            }


            if ($classification_name) {
                $i++;
                $condition[$i] = 'classification_name=' . "'" . $classification_name . "'";
            }

            if ($status!=NULL) {
                $i++;
                $condition[$i] = 'status=' . $status;
            }
            $i++;
            $condition[$i] = 'store_id=' . AccessTokenAuthentication::getUser(true);

            $result = Select::getexport($condition);

            Excel::export([
                'models' => $result,
                'fileName' => date('Ymd') . '-商品信息',
                'columns' => ['commodity_name', 'specification', 'commodity_code', 'classification_name', 'price', 'barcode', 'unit_name', 'default_depot_id', 'comment', 'created_time'],
                'headers' => [
                    'commodity_name' => '商品名称',
                    'specification' => '商品规格',
                    'commodity_code' => '商品ID',
                    'classification_name' => '分类名称',
                    'price' => '价格',
                    'barcode' => '条形码',
                    'unit_name' => '单位',
                    'default_depot_id' => '默认仓库',
                    'comment' => '备注信息',
                    'created_time' => '创建时间'
                ],
            ]);
        } catch (\Exception $ex) {
            $this->addError('getexport', 2012);
            return false;
        }
    }

}
