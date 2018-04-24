<?php

namespace commodity\modules\store\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\store\models\put\PutModel;

/**
 * Class PutController
 * @author hejinsong@9daye.com.cn
 * @package commodity\modules\store\controllers
 */
class PutController extends CommonController
{
    public $enableCsrfValidation = false;
    public $actionUsingDefaultProcess = [
        '_model' => PutModel::class
    ];

    public $access = [
    ];
}