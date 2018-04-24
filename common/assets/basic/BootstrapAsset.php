<?php
namespace common\assets\basic;

use yii\web\AssetBundle;

class BootstrapAsset extends AssetBundle{

    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
