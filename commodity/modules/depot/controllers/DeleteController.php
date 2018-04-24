<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/10
 * Time: 15:07
 */

namespace commodity\modules\depot\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\depot\models\delete\DeleteModel;

class DeleteController extends CommonController
{
    public $enableCsrfValidation = false;

    public $actionUsingDefaultProcess = [
        'index' => [
            'scenario' => DeleteModel::ACTION_INDEX,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DeleteModel::class
    ];

    public $access = [
        'index' => ['@','post']
    ];
}