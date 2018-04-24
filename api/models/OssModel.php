<?php
namespace api\models;

use Yii;
use common\models\Model;
use api\models\parts\OSSUploadCallback;

class OssModel extends Model{

    const SCE_GET_CALLBACK = 'get_callback';

    public $callbackTag;

    private $_responseData;

    public function scenarios(){
        return [
            self::SCE_GET_CALLBACK => [
                'callbackTag',
            ],
        ];
    }

    public function rules(){
        return [
            [
                ['callbackTag'],
                'required',
                'message' => 9001,
            ],
        ];
    }

    /**
     * 保存回调数据
     *
     * @return integer|false
     */
    public function saveCallbackData(){
        if(!$this->validate())return false;
        $OSSUploadCallback = new OSSUploadCallback();
        if(!$OSSUploadCallback->setOSSConfig($this->callbackTag)){
            $this->addError('saveCallbackData', 7002);
            return false;
        }
        if($callbackData = $OSSUploadCallback->getData()){
            $this->_responseData = $callbackData;
            return $OSSUploadCallback->saveData($callbackData);
        }else{
            $this->addError('saveCallbackData', 7001);
            return false;
        }
    }

    /**
     * 获取响应至客户端的数据
     *
     * @return array
     */
    public function getResponseData(){
        return [
            'url' => $this->getFullPathFilename(),
            'filename' => $this->_responseData['filename'],
        ];
    }

    /**
     * 获取完整的文件名
     *
     * @return string|false
     */
    private function getFullPathFilename(){
        if(isset($this->_responseData['filename'])){
            return Yii::$app->params['OSS_PostHost'] . '/' . $this->_responseData['filename'];
        }else{
            return false;
        }
    }
}
