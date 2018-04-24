<?php

/* *
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\store\controllers;

use common\controllers\Controller as BaseController;
use admin\modules\store\models\delete\DeleteModel;

class DeleteController extends BaseController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'del' => [
            'scenario' => DeleteModel::ACTION_DEL,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => DeleteModel::class,
    ];
    protected $access = [
        'del' => ['?', 'post'],
    ];
}