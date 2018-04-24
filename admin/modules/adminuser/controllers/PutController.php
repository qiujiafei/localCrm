<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qch <qianchaohui@9daye.com.cn>
 */

namespace admin\modules\adminuser\controllers;

use Yii;
use common\controllers\Controller as CommonController;
use admin\modules\adminuser\models\put\PutModel;

class PutController extends CommonController
{
    public $enableCsrfValidation = false;

    protected $access = [
        'user' => ['@', 'post'],
    ];
    
    public function actionUser()
    {
        $model = new PutModel([
            'scenario' => PutModel::ACTION_USER,
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

