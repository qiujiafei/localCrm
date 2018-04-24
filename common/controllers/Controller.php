<?php

namespace common\controllers;

use Yii;
use common\components\rbac\services\InvokableFactory;
use yii\web\ForbiddenHttpException;

class Controller extends BaseController
{
    protected $rbac;

    public function behaviors()
    {
        list($guest, $loggedIn, $requestMethod) = $this->getAllowRules();
        if(in_array($this->action->id, $loggedIn) && ! Yii::$app->user->getIsGuest()) {
            $this->rbac = (new InvokableFactory)($this);
            if(!$this->rbac->exists()) {
                throw new ForbiddenHttpException("没有权限.请通知管理员添加权限.");
            }
        }
        if(empty($this->access))return [];
        if(!$onlyActions = array_merge($guest, $loggedIn)){
            return [
                'verbs' => [
                    'class' => 'yii\filters\VerbFilter',
                    'actions' => $requestMethod,
                ],
            ];
        }
        $guestRule = $guest ? [
            'actions' => $guest,
            'allow' => true,
            'roles' => ['?']
        ] : null;
        $loggedInRule = $loggedIn ? [
            'actions' => $loggedIn,
            'allow' => true,
            'roles' => ['@']
        ] : null;
        $totalRules = [];
        is_null($guestRule) or $totalRules[] = $guestRule;
        is_null($loggedInRule) or $totalRules[] = $loggedInRule;
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'ruleConfig' => [
                    'class' => 'yii\filters\AccessRule',
                ],
                'only' => $onlyActions,
                'rules' => $totalRules,
                'denyCallback' => function($rule, $action){
                    throw new ForbiddenHttpException("用户未登录");
                }
            ],
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => $requestMethod,
            ],
        ];   
    }
}
