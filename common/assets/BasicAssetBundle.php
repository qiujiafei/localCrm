<?php
namespace common\assets;

use yii\web\AssetBundle;
use yii\base\InvalidParamException;

class BasicAssetBundle extends AssetBundle{

    private $_fileType = [
        'js',
        'css',
    ];

    public function addJs($files, $force = false){
        if(is_null($files))return $this;
        if($force){
            $this->forceAdd($this->js, $files);
        }else{
            $this->addFiles($files, 'js');
        }
        return $this;
    }

    public function addCss($files, $force = false){
        if(is_null($files))return $this;
        if($force){
            $this->forceAdd($this->css, $files);
        }else{
            $this->addFiles($files, 'css');
        }
        return $this;
    }

    protected function forceAdd(&$asset, $files){
        if(is_array($files)){
            foreach($files as $file){
                $asset[] = $file;
            }
        }else{
            $asset[] = (string)($files);
        }
    }

    protected function addFiles($files, $type){
        if(!$files)return false;
        if(!$this->verifyVariables($type))return false;
        is_array($files) or $files = (array)$files;
        foreach($files as $v){
            $this->addFile($v, $type);
        }
        return true;
    }

    protected function addFile($file, $type){
        if(in_array($file, $this->{'_' . $type})){
            $this->{$type}[] = $file;
            return true;
        }
        throw new InvalidParamException;
    }

    protected function verifyVariables($type){
        if(!in_array($type, $this->_fileType))return false;
        $privateVariable = isset($this->{'_' . $type}) ? $this->{'_' . $type} : null;
        $publicVariable = isset($this->{$type}) ? $this->{$type} : null;
        is_array($publicVariable) or $this->{$type} = [];
        return ($privateVariable && is_array($privateVariable));
    }

}
