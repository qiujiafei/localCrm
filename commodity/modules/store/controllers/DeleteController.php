<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/10
 * Time: 15:07
 */

namespace commodity\modules\store\controllers;

use common\controllers\Controller as CommonController;
use commodity\modules\store\models\delete\DeleteModel;

/**
 * Class DeleteController
 * @author hejinsong@9daye.com.cn
 * @package commodity\modules\store\controllers
 */
class DeleteController extends CommonController
{
    public $enableCsrfValidation = false;

    public $actionUsingDefaultProcess = [
        '_model' => DeleteModel::class
    ];

    public $access = [
        'index' => ['@','post']
    ];
}