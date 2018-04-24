<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\customercarstirebrand\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\customercarstirebrand\models\del\DelModel;

class DelController extends CommonController
{

    public $enableCsrfValidation = false;
    protected $actionUsingDefaultProcess = [
         'del' => [
            'scenario' => DelModel::ACTION_DEL,
            'method' => 'post',
            'convert' => false,
        ],
        
        '_model' => DelModel::class,
    ];
    protected $access = [
         'del' => ['@', 'post'],
    ];
}
