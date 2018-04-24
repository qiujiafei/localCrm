<?php

/* *
 * CRM system for 9daye
 *
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\supplier\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\supplier\models\get\GetModel;

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
        'down' => [
            'scenario' => GetModel::ACTION_DOWN,
            'method' => 'get',
            'convert' => false,
        ],
        '_model' => GetModel::class
    ];

    protected $access = [
        'lists' => ['@','get'],
        'one'  => ['@','get'],
        'down'  => ['@','get']
    ];
}
