<?php
namespace common\assets;

use yii\web\View;

class BaseAssetBundle extends \yii\web\AssetBundle{

    public function addFiles($target, $type = 'js'){
        if($target instanceof View){
            return $this->addJs($target)->addCss($target);
        }else{
            if($type == 'js'){
                return $this->addJs($target);
            }else{
                return $this->addCss($target);
            }
        }
    }

    public function addJs($target){
        if($target instanceof View){
            $jsFiles = $target->params['js'] ?? null;
        }else{
            $jsFiles = $target;
        }
        if($jsFiles){
            if($target instanceof View){
                if($addedJs = $this->verifiedAdd($jsFiles, 'js')){
                    $this->unsetFiles($target, $addedJs, 'js');
                }
            }else{
                $this->forceAdd($jsFiles, 'js');
            }
        }
        return $this;
    }

    public function addCss($target){
        if($target instanceof View){
            $cssFiles = $target->params['css'] ?? null;
        }else{
            $cssFiles = $target;
        }
        if($cssFiles){
            if($target instanceof View){
                if($addedCss = $this->verifiedAdd($cssFiles, 'css')){
                    $this->unsetFiles($target, $addedCss, 'css');
                }
            }else{
                $this->forceAdd($cssFiles, 'css');
            }
        }
        return $this;
    }

    protected function unsetFiles($viewObj, $files, $type){
        $viewObj->params[$type] = array_diff((array)$viewObj->params[$type], $files);
    }

    protected function verifiedAdd($files, $type){
        $fileList = $this->{'_' . $type} ?? null;
        $addedList = [];
        if($fileList){
            is_array($files) || $files = (array)$files;
            foreach($files as $file){
                if(in_array($file, $fileList)){
                    $this->addFile($file, $type);
                    $addedList[] = $file;
                }else{
                    throw new \Exception('Unknown file: ' . $file);
                }
            }
        }
        return $addedList;
    }

    protected function forceAdd($files, $type){
        if(is_array($files)){
            foreach($files as $file){
                $this->addFile($file, $type);
            }
        }else{
            $this->addFile($files, $type);
        }
    }

    protected function addFile($file, $type){
        $this->{$type}[] = $file;
    }
}
