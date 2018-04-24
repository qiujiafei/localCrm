<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use admin\modules\testAccount\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'getone' => [
            'scenario' => GetModel::ACTION_GETONE,
            'method' => 'get',
            'convert' => false,
        ],
        'get-accredit-all' => [
            'scenario' => GetModel::ACTION_GETACCREDITALL,
            'method' => 'get',
            'convert' => false,
        ],
         'get-forbidden-all' => [
            'scenario' => GetModel::ACTION_GETFORBIDDENALL,
            'method' => 'get',
            'convert' => false,
        ],
            '_model' => GetModel::class,
    ];
    protected $access = [
        'getone' => ['@', 'get'],
        'get-accredit-all' => ['', 'get'],
        'get-forbidden-all' => ['@', 'get'],
    ];
}
