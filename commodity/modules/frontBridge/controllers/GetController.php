<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\frontBridge\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\frontBridge\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'profile' => [
            'scenario' => GetModel::ACTION_GET_PROFILE,
            'method' => 'get',
            'convert' => false,
        ],
        '_model' => GetModel::class,
    ];
    protected $access = [
        'profile' => ['@', 'get'],
    ];
}
