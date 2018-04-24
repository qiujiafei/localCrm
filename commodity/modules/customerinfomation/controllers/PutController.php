<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customerinfomation\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\customerinfomation\models\put\PutModel;

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


