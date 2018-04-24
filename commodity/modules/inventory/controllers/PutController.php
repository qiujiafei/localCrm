<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\inventory\controllers;


use common\controllers\Controller as CommonController;
use commodity\modules\inventory\models\put\PutModel;

class PutController extends CommonController
{
    public $enableCsrfValidation = false;
    
    protected $actionUsingDefaultProcess = [
        'insert' => [
            'scenario' => PutModel::ACTION_INSERT,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => PutModel::class,
    ];
    
    protected $access = [
        'insert' => ['@', 'post'],
    ];
}


