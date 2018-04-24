<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace admin\modules\authorization\controllers;

use Yii;
use common\controllers\Controller;
use admin\modules\authorization\models\ResourceModel;

class ResourceController extends Controller
{

    public $enableCsrfValidation = false;

    protected $access = [
        'get-all'           => ['@', 'get'],
        'get-allow'         => ['@', 'get'],
        'assign-role'       => ['@', 'post'],
        'assign-resource'   => ['@', 'post'],

    ];

    public function actionGetAll()
    {
        $model = new ResourceModel([
            'scenario' => ResourceModel::GET_ALL,
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

    public function actionGetAllow()
    {
        $model = new ResourceModel([
            'scenario' => ResourceModel::GET_ALLOW,
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

    public function actionAssignRole()
    {
        $model = new ResourceModel([
            'scenario' => ResourceModel::ASSIGN_ROLE,
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

    public function actionAssignResource()
    {
        $model = new ResourceModel([
            'scenario' => ResourceModel::ASSIGN_RESOURCE,
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
