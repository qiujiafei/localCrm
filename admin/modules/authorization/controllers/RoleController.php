<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace admin\modules\authorization\controllers;

use Yii;
use common\controllers\Controller;
use admin\modules\authorization\models\RoleModel;

class RoleController extends Controller
{

    public $enableCsrfValidation = false;

    protected $access = [
        'getall'    => ['@', 'get'],
        'get-current-role' => ['@', 'get'],
        'add'       => ['@', 'post'],
        'delete'    => ['@', 'post'],
        'revoke'    => ['@', 'post'],
    ];

    public function actionGetall()
    {

        $model = new RoleModel([
            'scenario' => RoleModel::GET_ALL,
            'attributes' => array_merge(
                ['rbac' => $this->rbac]
            ), 
        ]);

        if(($processResult = $model->process()) !== false) {
            return $this->success($processResult === true ? [] : $processResult, false);
        } else {
            return $this->failure($model->errorCode);
        }
    }

    public function actionGetCurrentRole()
    {

        $model = new RoleModel([
            'scenario' => RoleModel::GET_CURRENT_ROLE,
            'attributes' => array_merge(
                ['rbac' => $this->rbac]
            ), 
        ]);

        if(($processResult = $model->process()) !== false) {
            return $this->success($processResult === true ? [] : $processResult, false);
        } else {
            return $this->failure($model->errorCode);
        }
    }

    public function actionAdd()
    {

        $model = new RoleModel([
            'scenario' => RoleModel::ADD,
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

    public function actionDelete()
    {

        $model = new RoleModel([
            'scenario' => RoleModel::DELETE,
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

    public function actionRevoke()
    {

        $model = new RoleModel([
            'scenario' => RoleModel::REVOKE,
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
