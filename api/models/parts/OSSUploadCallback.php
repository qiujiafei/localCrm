<?php
namespace api\models\parts;

use admin\models\parts\OSSUploadConfigForAdmin;
use custom\models\parts\OSSUploadConfigForCustom;
use Yii;
use ReflectionClass;
use supply\models\parts\OSSUploadConfigForSupply;

class OSSUploadCallback{

    //回调标记
    const CALLBACK_TAG = 'callback_tag';
    //回调身份
    const CALLBACK_IDENTITY = 'callback_identity';

    //OSS配置对象
    protected $OSSConfig;

    /**
     * 设置OSS配置对象
     *
     * @param $callbackTag 回调标记
     * @return boolean
     */
    public function setOSSConfig($callbackTag){
        if(in_array($callbackTag, $tags = $this->getTags())){
            $config = array_flip($tags);
            $OSSConfig = new ReflectionClass($config[$callbackTag]);
            return ($this->OSSConfig = $OSSConfig->newInstance()) ? true : false;
        }else{
            return false;
        }
    }

    /**
     * 获取所有回调标记
     *
     * @return array
     */
    public static function getTags(){
        return [
            OSSUploadConfigForSupply::className() => OSSUploadConfigForSupply::getCallbackTag(),
            OSSUploadConfigForAdmin::className() => OSSUploadConfigForAdmin::getCallbackTag(),
            OSSUploadConfigForCustom::className()=>OSSUploadConfigForCustom::getCallbackTag(),
        ];
    }

    /**
     * 获取过滤后的回调数据
     *
     * @return array
     */
    public function getData(){
        $shouldCallback = $this->OSSConfig->getOSSCallbackParams();
        return $this->filterData($shouldCallback, Yii::$app->request->post());
    }

    /**
     * 根据OSS配置对象分别保存回调数据
     *
     * @return boolean
     */
    public function saveData($callbackData){
        return $this->OSSConfig->saveData($callbackData);
    }

    /**
     * 过滤回调数据
     * 如果回调数据错误返回false
     *
     * @return array|false
     */
    protected function filterData($shouldCallback, $actualCallback){
        $intersectCallback = array_intersect_key($actualCallback, $shouldCallback);
        if(!isset($intersectCallback[self::CALLBACK_IDENTITY]) || !$intersectCallback[self::CALLBACK_IDENTITY])return false;
        if(($callbackCount = count($intersectCallback)) == count($shouldCallback) && $callbackCount > 0){
            return $intersectCallback;
        }else{
            return false;
        }
    }
}
