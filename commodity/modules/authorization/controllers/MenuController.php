<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\authorization\controllers;

use Yii;
use common\controllers\Controller;
use commodity\modules\authorization\models\MenuModel;

class MenuController extends Controller
{

    public $enableCsrfValidation = false;

    protected $access = [
        'getall'    => ['@','get'],
    ];

    public function actionGetall()
    {

        $model = new MenuModel([
            'scenario' => MenuModel::GET_ALL_MENU,
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

}
