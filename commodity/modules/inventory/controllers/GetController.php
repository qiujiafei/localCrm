<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\inventory\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\inventory\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
         'lists' => [
            'scenario' => GetModel::ACTION_LISTS,
            'method' => 'get',
            'convert' => false,
        ],
         'one' => [
            'scenario' => GetModel::ACTION_ONE,
            'method' => 'get',
            'convert' => false,
        ],
        'number' => [
            'scenario' => GetModel::ACTION_NUMBER,
            'method' => 'get',
            'convert' => false,
        ],
        'commodity' => [
            'scenario' => GetModel::ACTION_COMMODITY,
            'method' => 'get',
            'convert' => false,
        ],
        //盘点单详情列表
        'inventory-commodity' => [
            'scenario' => GetModel::ACTION_COMMODITY_LISTS,
            'method' => 'get',
            'convert' => false,
        ],
        'statistics' => [
            'scenario' => GetModel::ACTION_STATISTICS,
            'method' => 'get',
            'convert' => false,
        ],
        'allow-commodity' => [
            'scenario' => GetModel::ACTION_ALLOW_COMMODITY,
            'method' => 'get',
            'convert' => false,
        ],
        
        '_model' => GetModel::class,
    ];
    protected $access = [
        'lists' => ['@', 'get'],
        'one' => ['@', 'get'],
        'number' => ['@','get'],
        'commodity' => ['@','get'],
        'allow-commodity' => ['@','get'],
        'statistics' => ['@', 'get'],
    ];
}
