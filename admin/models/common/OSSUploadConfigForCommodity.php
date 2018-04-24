<?php
namespace admin\models\common;

use common\models\Abstraction\OSSUploadConfigAbstract;
use common\ActiveRecord\OSSUploadFileAR;

class OSSUploadConfigForadmin extends OSSUploadConfigAbstract
{

    //用户ID
    public $userId;

    public static function getCallbackTag()
    {
        return 'admin';
    }

    /**
     * 获取上传限制
     * @return array
     */
    public function getUploadLimit()
    {
        return [
            'img_min_length' => $this->fileMinLength,
            'img_max_length' => $this->fileMaxLength,
            'img_suffix' => $this->getAuthorizeSuffix(),
        ];
    }

    protected function getFilePrefix()
    {
        return "a_{$this->userId}/";
    }

    protected function getCallbackIdentityId()
    {
        return $this->userId;
    }

    protected function getAuthorizeSuffix()
    {
        return [
            'jpg',
            'jpeg',
            'png',
            'gif',
        ];
    }

    protected function getUploaderType()
    {
        return OSSUploadFileAR::admin;
    }

    protected function getExtraCallbackParams()
    {
        return [];
    }
}
