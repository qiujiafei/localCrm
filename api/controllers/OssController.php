<?php
namespace api\controllers;

use Yii;
use common\controllers\Controller;
use api\models\OssModel;
use api\models\parts\OSSUploadCallback;

class OssController extends Controller{

    protected $access = [
        'callback' => [null, 'post'],
    ];

    /**
     * OSS回调至服务器 处理接口
     */
    public function actionCallback(){
        $OssModel = new OssModel([
            'scenario' => OssModel::SCE_GET_CALLBACK,
            'callbackTag' => Yii::$app->request->post(OSSUploadCallback::CALLBACK_TAG),
        ]);
        if($OssModel->saveCallbackData()){
            return $this->success($OssModel->getResponseData());
        }else{
            return $this->failure($OssModel->getErrorCode());
        }
    }
}
