<?php

namespace commodity\modules\supplier\controllers;
use common\controllers\Controller as CommonController;
use commodity\modules\supplier\models\put\PutModel;

class PutController extends CommonController
{
    public $enableCsrfValidation = false;

    protected $actionUsingDefaultProcess = [
        'insert' => [
            'scenario' => PutModel::ACTION_INSERT,
            'method' => 'post',
            'convert' => false,
        ],

        '_model' => PutModel::class
    ];

    protected $access = [
        'insert' => ['@','post']
    ];
}