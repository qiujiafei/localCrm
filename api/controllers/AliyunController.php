<?php
namespace api\controllers;


use api\models\AliyunModel;
use common\controllers\Controller;
use Yii;

class AliyunController extends Controller
{
    protected $access = [
        'order-rank'=>[null,'get'],//门店累计采购排行
        'fly-line'=>[null,'get'],//飞线
        'breath-bubble'=>[null,'get'],//气泡
        'hot-area'=>[null,'get'],//热力区
        'sale-amount-speed'=>[null,'get'],//销售额增速
        'city-sale-rank'=>[null,'get'],//城市销售前8
        'district-sale-rank'=>[null,'get'],//区域销售前8
        'hot-product'=>[null,'get'],//最新售出商品战报
        'city-max-sale'=>[null,'get'],//当前小时销售额最高的二级区域
        'district-max-sale'=>[null,'get'],//当前小时销售额最高的三级区域
        'stock-warning'=>[null, 'get'],//库存预警
        'province-sale'=>[null, 'get'],//给省累计销售情况
        'sale-total-price'=>[null, 'get'],//当日有效的销售总额
        'sum-custom'=>[null, 'get'],//当日累计参与消费的门店数
        'sum-product'=>[null, 'get'], //累计售出商品数量
        'supply-hour-first'=>[null, 'get'],//单小时销售冠军  供应商
        'supply-sale-five'=>[null, 'get'], //营业额前五 供应商
    ];
    protected $actionUsingDefaultProcess = [
        'order-rank'=>AliyunModel::SCE_ORDER_RANK,
        'fly-line'=>AliyunModel::SCE_FLY_LINE,
        'breath-bubble'=>AliyunModel::SCE_BREATH_BUBBLE,
        'hot-area'=>AliyunModel::SCE_HOT_AREA,
        'sale-amount-speed'=>AliyunModel::SCE_SALE_AMOUNT_SPEED,
        'city-sale-rank'=>AliyunModel::SCE_CITY_SALE_RANK,
        'district-sale-rank'=>AliyunModel::SCE_DISTRICT_SALE_RANK,
        'hot-product'=>AliyunModel::SCE_HOT_PRODUCT,
        'city-max-sale'=>AliyunModel::SCE_CITY_MAX_SALE,
        'district-max-sale'=>AliyunModel::SCE_DISTRICT_MAX_SALE,
        'stock-warning'=>AliyunModel::SCE_STOCK_WARNING,
        'province-sale'=>AliyunModel::SCE_PROVINCE_SALE,
        'sale-total-price'=>AliyunModel::SCE_SALE_TOTAL_PRICE,
        'sum-custom'=>AliyunModel::SCE_SUM_CUSTOM,
        'sum-product'=>AliyunModel::SCE_SUM_PRODUCT,
        'supply-hour-first'=>AliyunModel::SCE_SUPPLY_HOUR_FIRST,
        'supply-sale-five'=>AliyunModel::SCE_SUPPLY_SALE_FIVE,
        '_model'=>'\api\models\AliyunModel'
    ];


    protected function returnJson($code, $param, $convert){
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');
        Yii::$app->response->headers->set('Access-Control-Allow-Methods', 'GET');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if($convert){
            $param = $this->convertNumericType($param);
        }
        return $param;
    }
}