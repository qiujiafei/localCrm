<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\employee\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\employee\models\get\GetModel;

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
        'getemployee' => [
            'scenario' => GetModel::ACTION_GETEMPLOYEE,
            'method' => 'get',
            'convert' => false,
        ],
            '_model' => GetModel::class,
    ];
    protected $access = [
        'getone' => ['@', 'get'],
        'getall' => ['@', 'get'],
        'getemployee' => ['@', 'get'],
    ];

}
