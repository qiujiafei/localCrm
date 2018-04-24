<?php

/* * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use admin\modules\adminuser\models\get\GetModel;

class GetController extends CommonController
{

    public $enableCsrfValidation = false;

    protected $access = [
        'getone' => ['@', 'get'],
        'getall' => ['@', 'get'],
    ];

    public function actionGetone()
    {
        $model = new GetModel([
            'scenario' => GetModel::ACTION_GETONE,
            'attributes' => array_merge(
                yii::$app->request->get(),
                ['rbac' => $this->rbac]
            ),
        ]);

        if(($processResult = $model->process()) !== false) {
            return $this->success($processResult === true ? [] : $processResult, false);
        } else {
            return $this->failure($model->errorCode);
        }
    }

    public function actionGetall()
    {
        $model = new GetModel([
            'scenario' => GetModel::ACTION_GETALL,
            'attributes' => array_merge(
                yii::$app->request->get()
            ),
        ]);

        if(($processResult = $model->process()) !== false) {
            return $this->success($processResult === true ? [] : $processResult, false);
        } else {
            return $this->failure($model->errorCode);
        }
    }
}
