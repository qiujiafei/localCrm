<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:14
 */

namespace commodity\modules\store\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\store\models\get\GetModel;

class GetController extends CommonController
{
    public $enableCsrfValidation = false;
    public $actionUsingDefaultProcess = [
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
        '_model' => GetModel::class
    ];

    public $access = [
        'lists' => ['@','get'],
        'one' => ['@','get'],
        'get-accredit-all' => ['@', 'get'],
        'get-forbidden-all' => ['@', 'get'],
    ];
}