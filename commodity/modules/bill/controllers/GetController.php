<?php
/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */
namespace commodity\modules\bill\controllers;

use commodity\modules\bill\models\get\GetModel;
use common\controllers\Controller;

class GetController extends Controller {

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'lists' => [
            'scenario' => GetModel::ACTION_LISTS,
            'method' => 'get',
            'convert' => false,
        ],
        'getone' => [
            'scenario' => GetModel::ACTION_GETONE,
            'method' => 'get',
            'convert' => false,
        ],
        'get-no-account' => [
            'scenario' => GetModel::ACTION_GETNOACCOUNT,
            'method' => 'get',
            'convert' => false,
        ],
        'get-account' => [
            'scenario' => GetModel::ACTION_GETACCOUNT,
            'method' => 'get',
            'convert' => false,
        ],
        'getexport' => [
            'scenario' => GetModel::ACTION_GETEXPORT,
            'method' => 'get',
            'convert' => false,
        ],
        'get-bill-members' => [
            'scenario' => GetModel::ACTION_GETBILLMEBERS,
            'method' => 'get',
            'convert' => false,
        ],
        'customer' => [
            'scenario' => GetModel::ACTION_CUSTOMER,
            'method' => 'get',
            'convert' => false,
        ],
            '_model' => GetModel::class
    ];
    protected $access = [
        'lists' => ['@', 'get'],
        'getone' => ['@', 'get'],
        'get-no-account' => ['@', 'get'],
        'get-account' => ['@', 'get'],
        'getexport' => ['@', 'get'],
        'get-bill-members' => ['@', 'get'],
        'customer' => ['@','get']
    ];

}
