<?php

$params = array_merge(
        require __DIR__ . '/../../common/config/params.php', require __DIR__ . '/../../common/config/params-local.php', require __DIR__ . '/params.php', require __DIR__ . '/params-local.php'
);

return [
    'id' => 'admin',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'admin\controllers',
    'bootstrap' => [
        'adminuser',
        'authentication',
        'token',
        'store',
        'testAccount',
        'authorization',
        'sprider',
    ],
    'modules' => [
        'adminuser' => 'admin\modules\adminuser\Module',
        'authentication' => 'admin\modules\authentication\Module',
        'store' => 'admin\modules\store\Module',
        'testAccount' => 'admin\modules\testAccount\Module',
        'authorization' => 'admin\modules\authorization\Module',
        'sprider' => 'admin\modules\sprider\Module',
    ],
    'components' => [
        'user' => [
            'identityClass' => 'admin\components\tokenAuthentication\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => '/'
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'admin',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'index/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'suffix' => '.do',
            'rules' => [
                'captcha' => 'index/captcha',
                'error' => 'index/error',
                'api-hostname' => 'index/api-hostname',
                '' => 'index',
                '<controller:[a-z-]+>' => '<controller>',
                '<controller:[a-z-]+>/<action:[a-z-]+>' => '<controller>/<action>',
            ],
        ],
        'token' => [
            'class' => 'admin\components\tokenAuthentication\AccessTokenAuthentication',
            'dbHandler' => 'admin\models\common\UserModel',
            'tokenName' => 'token',
            'tokenTimeout' => '3600'
        ],
    ],
    'params' => $params,
];
