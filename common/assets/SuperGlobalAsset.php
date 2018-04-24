<?php
namespace common\assets;

use yii\web\AssetBundle;

class SuperGlobalAsset extends AssetBundle{

    public $sourcePath = '@common/assets/SuperGlobalAssets';
    public $css = [
        'css/Sglobal.css',
        'css/dropdownListForExpress.css',
        'css/button.css',
        'css/colors.css'
    ];
    public $js = [
        'js/Sglobal.js',
        'js/commonApi.js',
        'js/dropdownListForExpress.js',
        'js/uploadImg.js',
        'js/addScripts.js',
        'js/screenIndication.js'
    ];

    public $depends = [
        'common\assets\basic\BootstrapAsset',
        'common\assets\basic\JuicerAsset',
    ];
}
