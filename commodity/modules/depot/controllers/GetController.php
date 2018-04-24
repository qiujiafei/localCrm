<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:14
 */

namespace commodity\modules\depot\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\depot\models\get\GetModel;

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
        '_model' => GetModel::class
    ];

    public $access = [
        'lists' => ['@','get'],
        'one' => ['@','get'],
    ];
}