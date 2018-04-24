<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\controllers;

use common\controllers\Controller as BaseController;
use commodity\modules\commodityManage\models\delete\DeleteModel;

class DeleteController extends BaseController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'one' => [
            'scenario' => DeleteModel::ACTION_ONE,
            'method' => 'post',
            'convert' => false,
        ],
        'batch' => [
            'scenario' => DeleteModel::ACTION_BATCH,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DeleteModel::class,
    ];
    protected $access = [
        'one' => ['@', 'post'],
        'batch' => ['@', 'post'],
    ];
}