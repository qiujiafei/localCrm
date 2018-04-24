<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:14
 */

namespace commodity\modules\depot\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\depot\models\put\PutModel;

class PutController extends CommonController
{
    public $enableCsrfValidation = false;
    public $actionUsingDefaultProcess = [
        'insert' => [
            'scenario' => PutModel::ACTION_INSERT,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => PutModel::class
    ];

    public $access = [
        'insert' => ['@','post']
    ];
}