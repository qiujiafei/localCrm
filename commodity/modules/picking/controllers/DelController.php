<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\picking\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use commodity\modules\picking\models\del\DelModel;

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
