<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=chuangzhihui.9daye.com.cn;port=3307;dbname=crm_v2',
            'username' => 'crm',
            'password' => 'Crm1234321',
            'charset' => 'utf8mb4',
            'tablePrefix' => 'crm_',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
