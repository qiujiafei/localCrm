<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\purchase\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\purchase\models\del\DeleteModel;

class DeleteController extends CommonController
{

    public $enableCsrfValidation = false;

    protected $actionUsingDefaultProcess = [
         'index' => [
            'scenario' => DeleteModel::ACTION_INDEX,
            'method' => 'post',
            'convert' => false,
        ],
        
        '_model' => DeleteModel::class,
    ];
    protected $access = [
         'index' => ['@', 'post'],
    ];
}
