<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\purchase\models\get\GetModel;

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

        'last-price' => [
            'scenario' => GetModel::ACTION_LAST_PRICE,
            'method' => 'get',
            'convert' => false,
        ],
        'detail' => [
            'scenario' => GetModel::ACTION_DETAIL,
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
        'last-price' => ['@','get'],
        'detail' => ['@','get'],
        'allow-commodity' => ['@','get']
    ];
}
