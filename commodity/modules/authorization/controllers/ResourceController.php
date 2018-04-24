<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\authorization\controllers;

use Yii;
use common\controllers\Controller;
use commodity\modules\authorization\models\ResourceModel;

class ResourceController extends Controller
{

    public $enableCsrfValidation = false;

    protected $access = [
        'getall'    => ['@','get'],
        'modify'    => ['@','post'],
    ];

    public function actionGetall()
    {

        $model = new ResourceModel([
            'scenario' => ResourceModel::GET_ALL_RESOURCE,
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

    public function actionModify()
    {
        $model = new ResourceModel([
            'scenario' => ResourceModel::MODIFY,
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
