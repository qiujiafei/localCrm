<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/24
 * Time: 9:08
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\finance\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\finance\models\get\GetModel;

class GetController extends CommonController
{
    public $enableCsrfValidation = false;

    public $actionUsingDefaultProcess = [

        'turnover' => [
            'scenario' => GetModel::ACTION_TURNOVER,
            'method' => 'get',
            'convert' => false,
        ],

        'purchase-amount' => [
            'scenario' => GetModel::ACTION_PURCHASE_AMOUNT,
            'method' => 'get',
            'convert' => false,
        ],

        'purchase-supplier' => [
            'scenario' => GetModel::ACTION_PURCHASE_SUPPLIER,
            'method' => 'get',
            'convert' => false,
        ],

        'frequency-of-store-visit' => [
            'scenario' => GetModel::ACTION_FREQUENCY_OF_STORE_VISIT,
            'method' => 'get',
            'convert' => false,
        ],

        'customers' => [
            'scenario' => GetModel::ACTION_CUSTOMERS,
            'method' => 'get',
            'convert' => false,
        ],

        'turnover-lists' => [
            'scenario' => GetModel::ACTION_TURNOVER_LISTS,
            'method' => 'get',
            'convert' => false,
        ],

        'purchase-amount-lists' => [
            'scenario' => GetModel::ACTION_PURCHASE_AMOUNT_LISTS,
            'method' => 'get',
            'convert' => false,
        ],

        'frequency-of-store-visit-lists' => [
            'scenario' => GetModel::ACTION_FREQUENCY_OF_STORE_VISIT_LISTS,
            'method' => 'get',
            'convert' => false,
        ],

        'purchase-statistics-lists' => [
            'scenario' => GetModel::ACTION_PURCHASE_STATISTICS_LISTS,
            'method' => 'get',
            'convert' => false,
        ],

        'service-lists' => [
            'scenario' => GetModel::ACTION_SERVICE_LISTS,
            'method' => 'get',
            'convert' => false,
        ],


        '_model' => GetModel::class,
    ];
    public $access = [
        //营业额
        'turnover' => ['@','get'],
        //采购金额
        'purchase-amount' => ['@','get'],
        //采购应付
        'purchase-supplier' => ['@','get'],
        //到店次数
        'frequency-of-store-visit' => ['@','get'],
        //客户
        'customers' => ['@','get'],
        //营业额列表明细
        'turnover-lists' => ['@','get'],
        //采购金额明细
        'purchase-amount-lists' => ['@','get'],
        //到店次数明细
        'frequency-of-store-visit-lists' => ['@','get'],
        //采购统计
        'purchase-statistics-lists' => ['@','get'],
        //施工统计
        'service-lists' => ['@','get']
    ];

}