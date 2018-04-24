<?php

/**
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\controllers;

use Yii;
use common\controllers\Controller as BaseController;
use admin\modules\adminuser\models\delete\DeleteModel;

class DeleteController extends BaseController
{
    public $enableCsrfValidation = false;
    
    protected $access = [
        'one' => ['@', 'post'],
    ];

    public function actionOne()
    {
        $model = new DeleteModel([
            'scenario' => DeleteModel::ACTION_ONE,
            'attributes' => array_merge(
                yii::$app->request->post(),
                ['rbac' => $this->rbac]
            ),
        ]);

        if(($processResult = $model->process()) !== false) {
            return $this->success($processResult === true ? [] : $processResult, false);
        } else {
            return $this->failure($model->errorCode);
        }
    }
}