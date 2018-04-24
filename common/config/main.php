<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'time' => [
            'class' => 'common\components\RapidTime',
        ],
        'RQ' => [
            'class' => 'common\models\RapidQuery',
        ],
        'EC' => [
            'class' => 'common\components\ErrCallback',
        ],
    ],
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
];
