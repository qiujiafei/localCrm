<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\export\models\data;

use common\models\Model as CommonModel;
use common\components\handler\ExcelHandler;
use Yii;

class DataModel extends CommonModel {

    const ACTION_CUSTORMER = 'action_customer';
    const ACTION_SERVICE = 'action_service';
    const ACTION_COMMODITY = 'action_commodity';

    public $token;

    public function scenarios() {
        return [
            self::ACTION_CUSTORMER => [
                'token'
            ],
            self::ACTION_SERVICE => [
                'token'
            ],
            self::ACTION_COMMODITY => [
                'token'
            ],
        ];
    }

    /**
     * 客户资料导入模板
     * 
     * @return bool 
     */
    public function actionCustomer() {

        try {
            $title_data = [
                '客户姓名',
                '性别',
                '地址',
                '身份证',
                '生日',
                '手机号码',
                '客户来源',
                '单位名称',
                '备注',
                '车架号',
                '车牌号省份',
                '车牌号字母',
                '车牌号',
                '汽车型号',
                '排量',
                '车价',
                '发动机型号',
                '发动机号码',
                '厂牌型号',
                '漏油检查',
                '下次保养里程',
                '上次保养里程',
                '轮胎检查',
                '车辆颜色',
                '刹车片检查',
                '刹车油检查',
                '电瓶检查',
                '机油检查',
                '保险公司',
                '故障灯检查',
                '轮胎品牌',
                '轮胎型号',
            ];
            ExcelHandler::output(array(), $title_data, date('Y_m_d') . '客户资料导入模板');

            return [];
        } catch (\Exception $ex) {
            echo $ex;
            die;
            $this->addError('exportcustomer', $ex);
            return false;
        }
    }

    /**
     * 服务导入模板
     * 
     * @return bool 
     */
    public function actionService() {

        try {

            $title_data = [
                '服务项目名称',
                '规格',
                '销售价格',
                '自助项目',
                '服务项目分类',
                '备注',
            ];

            ExcelHandler::output(array(), $title_data, date('Y_m_d') . '服务导入模板');

            return [];
        } catch (\Exception $ex) {
            $this->addError('exportservice', $ex);
            return false;
        }
    }

    /**
     * 商品导入模板
     * 
     * @return bool 
     */
    public function actionCommodity() {
        try {

            $title_data = [
                '商品名称',
                '商品规格',
                '商品编码',
                '分类名称',
                '价格',
                '条形码',
                '单位名称',
                '配件属性',
                '备注',
                '来源',
                '默认仓库',
            ];
            ExcelHandler::output(array(), $title_data, date('Y_m_d') . '商品导入模板');
            return [];
        } catch (\Exception $ex) {
            $this->addError('exportcommodity', $ex);
            return false;
        }
    }

}
