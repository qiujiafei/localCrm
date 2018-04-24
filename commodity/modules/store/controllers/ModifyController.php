<?php

namespace commodity\modules\store\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\store\models\modify\ModifyModel;

/**
 * Class ModifyController
 * @author hejinsong@9daye.com.cn
 * @package commodity\modules\store\controllers
 */
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