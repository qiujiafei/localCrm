<?php
namespace common\assets;

class AjaxAsset extends BasicAssetBundle{

    public $sourcePath;
    public $js = [];
    public $css = [];

    public __construct($sourcePath){
        $this->sourcePath = $sourcePath;
    }

    public function show($view){
        $this->init();
        $this->publish($view->getAssetManager());
        $this->registerAssetFiles($view);
    }

    public static register($view){
        throw new \Exception('you must instance this object');
    }

}
