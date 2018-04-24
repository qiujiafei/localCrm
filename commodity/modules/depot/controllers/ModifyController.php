<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 17:02
 */

namespace commodity\modules\depot\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\depot\models\modify\ModifyModel;

class ModifyController extends CommonController
{
    public $enableCsrfValidation = false;
    public $actionUsingDefaultProcess = [
        'edit' => [
            'scenario' => ModifyModel::ACTION_EDIT,
            'method' => 'post',
            'convert' => false,
        ],
        '_model' => ModifyModel::class
    ];

    public $access = [
        'edit' => ['@','post']
    ];
}