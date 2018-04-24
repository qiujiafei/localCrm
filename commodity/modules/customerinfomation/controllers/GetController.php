<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\customerinfomation\models\get\GetModel;

class GetController extends CommonController {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'getone' => [
            'scenario' => GetModel::ACTION_GETONE,
            'method' => 'get',
            'convert' => false,
        ],
        'getall' => [
            'scenario' => GetModel::ACTION_GETALL,
            'method' => 'get',
            'convert' => false,
        ],
        'getexport' => [
            'scenario' => GetModel::ACTION_GETEXPORT,
            'method' => 'get',
            'convert' => false,
        ],
        'getmemberstatistics' => [
            'scenario' => GetModel::ACTION_GETMEMBERSTATISTICS,
            'method' => 'get',
            'convert' => false,
        ],
        'getallmember' => [
            'scenario' => GetModel::ACTION_GETALLMEMBER,
            'method' => 'get',
            'convert' => false,
        ],
        'getonemember' => [
            'scenario' => GetModel::ACTION_GETONEMEMBER,
            'method' => 'get',
            'convert' => false,
        ],
            '_model' => GetModel::class,
    ];
    protected $access = [
        'getone' => ['@', 'get'],
        'getall' => ['@', 'get'],
        'getexport' => ['@', 'get'],
        'getallmember' => ['@', 'get'],
        'getmemberstatistics' => ['@', 'get'],
        'getonemember' => ['@', 'get'],
    ];

}
