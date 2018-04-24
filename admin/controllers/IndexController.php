<?php

/** 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace admin\controllers;

use common\controllers\Controller as CommonController;
use common\models\ErrorModel;
use Yii;

class IndexController extends CommonController
{
    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
        'error' => [
            'scenario' => ErrorModel::ACTION_ERROR,
            'method' => 'get',
            'convert' => false,
        ],
        '_model' => ErrorModel::class,
    ];
}