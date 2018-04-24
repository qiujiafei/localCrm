<?php
namespace common\models\Abstraction;

use Yii;
use DateTime;
use yii\base\Object;
use common\models\UniqueString;
use api\models\parts\OSSUploadCallback;
use common\models\RapidQuery;
use common\ActiveRecord\OSSUploadFileAR;
use common\models\FileMimetype;

abstract class OSSUploadConfigAbstract extends Object{

    /**
     * OSS验证类型
     *
     * STRICT 完全匹配
     * STARTSWITH 文件名前N个字符匹配
     */
    const STRICT_VALIDATION = 0;
    const STARTSWITH_VALIDATION = 1;

    //生成的随机文件名
    private $_fileNames;

    //OSS连接ID
    protected $id;
    //OSS连接密钥
    protected $key;
    //OSS上传地址
    protected $host;
    //OSS库名称
    protected $bucket;
    //回调地址
    protected $callbackUrl;
    //回调主体格式
    protected $callbackBodyType = 'application/x-www-form-urlencoded';

    //permission有效时间 秒
    public $expire = 10;
    //文件尺寸最小值
    public $fileMinLength = 1;
    //文件尺寸最大值
    public $fileMaxLength = 2097152;
    //上传文件的验证方法
    public $validationType = self::STRICT_VALIDATION;
    //可上传的文件数量 主要用于生成随机文件名
    public $fileCount = 1;
    //上传文件的后缀
    public $fileSuffix;

    /**
     * 获取文件前缀
     * 若前缀以'/'结尾，则在OSS中表现为文件夹
     *
     * @return string
     */
    abstract protected function getFilePrefix();

    /**
     * 回调时提供的用户身份
     *
     * @return array
     */
    abstract protected function getCallbackIdentityId();

    /**
     * 获取可上传的文件后缀
     *
     * @return array
     */
    abstract protected function getAuthorizeSuffix();

    /**
     * 获取回调标记
     *
     * @return string
     */
    abstract public static function getCallbackTag();

    /**
     * 获取上传者类型
     *
     * @return integer
     */
    abstract protected function getUploaderType();

    /**
     * 获取额外的回调参数
     *
     * @return array
     */
    abstract protected function getExtraCallbackParams();

    /**
     * 初始化OSS信息
     */
    public function init(){
        $this->id = Yii::$app->params['OSS_AccessKeyId'];
        $this->key = Yii::$app->params['OSS_AccessKeySecret'];
        $this->host = Yii::$app->params['OSS_PostHost'];
        $this->bucket = Yii::$app->params['OSS_Bucket'];
        $this->callbackUrl = Yii::$app->params['OSS_CallbackUrl'];
    }

    /**
     * 获取授权信息
     *
     * @return array
     */
    public function getPermission(){
        if($this->validationType == self::STRICT_VALIDATION){
            if(!$this->verifyFileSuffix())return false;
        }else{
            if($this->fileCount <= 0)return false;
        }
        return [
            'OSSAccessKeyId' => $this->id,
            'host' => $this->host,
            'policy' => $this->getPolicy(),
            'signature' => $this->getSignature(),
            'expire' => $this->getEndTime(),
            'callback' => $this->getCallback(),
            'key' => $this->getFileNames(),
        ];
    }

    /**
     * 获取OSS回调提供的参数
     *
     * @return array
     */
    public function getOSSCallbackParams(){
        return array_merge($this->getCallbackBodyCommon(), $this->getCallbackBodyTag(), $this->getCallbackIdentity(), $this->getExtraCallbackParams());
    }

    /**
     * 保存回调数据
     *
     * @return boolean
     */
    public function saveData($callbackData){
        return (new RapidQuery(new OSSUploadFileAR))->insert([
            'filename' => $callbackData['filename'],
            'upload_user_type' => $this->getUploaderType(),
            'upload_user_id' => $callbackData[OSSUploadCallback::CALLBACK_IDENTITY],
            'size' => $callbackData['size'],
            'file_mimetype_id' => FileMimetype::getId($callbackData['mimeType'], true),
            'width' => $callbackData['width'] ? : 0,
            'height' => $callbackData['height'] ? : 0,
        ]);
    }

    /**
     * 获取文件名称
     *
     * @param $refresh 重新获取
     * @return array
     */
    protected final function getFileNames($refresh = false){
        if($refresh)$this->_fileNames = null;
        if(is_null($this->_fileNames)){
            $this->_fileNames = $this->getRandomFileNames();
            if($this->validationType == self::STRICT_VALIDATION){
                $this->_fileNames = array_map(function($filename){
                    return $filename . '.' . strtolower(ltrim($this->fileSuffix, '.'));
                }, $this->_fileNames);
            }
        }
        return $this->_fileNames;
    }

    /**
     * 验证文件后缀
     *
     * @return boolean
     */
    protected function verifyFileSuffix(){
        $suffix = strtolower(ltrim($this->fileSuffix, '.'));
        $authorizeSuffix = array_map(function($suffix){
            return strtolower(ltrim($suffix, '.'));
        }, $this->getAuthorizeSuffix());
        return in_array($suffix, $authorizeSuffix);
    }

    /**
     * 获取随机文件名
     *
     * @return array
     */
    protected function getRandomFileNames(){
        $uniqueString = new UniqueString([
            'extraPrefix' => $this->getFilePrefix(),
            'suffix' => 3,
            'extraSuffix' => 1,
            'mode' => 'microtime'
        ]);
        return (array)$uniqueString->hash($this->validationType == self::STRICT_VALIDATION ? 1 : $this->fileCount);
    }

    /**
     * 获取回调
     *
     * @return string
     */
    protected function getCallback(){
        $callbackParam = $this->getCallbackParam();
        return base64_encode(json_encode($callbackParam));
    }

    protected function getCallbackIdentity(){
        return [
            OSSUploadCallback::CALLBACK_IDENTITY => $this->getCallbackIdentityId(),
        ];
    }

    /**
     * 获取回调参数
     *
     * @return array
     */
    protected function getCallbackParam(){
        return [
            'callbackUrl' => $this->callbackUrl,
            'callbackBody' => $this->getCallbackBody(),
            'callbackBodyType' => $this->callbackBodyType,
        ];
    }

    /**
     * 获取回调主体
     *
     * @return string
     */
    protected function getCallbackBody(){
        $callbackParams = $this->getOSSCallbackParams();
        $body = [];
        foreach($callbackParams as $k => $v){
            $body[] = $k . '=' . $v;
        }
        return implode('&', $body);
    }

    /**
     * 获取回调主体标记，以识别提交者身份
     */
    protected function getCallbackBodyTag(){
        return [
            OSSUploadCallback::CALLBACK_TAG => static::getCallbackTag(),
        ];
    }

    /**
     * 获取通用回调参数
     *
     * @return array
     */
    protected function getCallbackBodyCommon(){
        return [
            'filename' => '${object}',
            'size' => '${size}',
            'mimeType' => '${mimeType}',
            'height' => '${imageInfo.height}',
            'width' => '${imageInfo.width}',
        ];
    }

    /**
     * 获取签名
     *
     * @return string
     */
    protected function getSignature(){
        return base64_encode(hash_hmac('sha1', $this->getPolicy(), $this->key, true));
    }

    /**
     * 获取许可信息
     *
     * @return string
     */
    protected function getPolicy(){
        $policy = [
            'expiration' => $this->getExpiration(),
        ];
        if($conditions = $this->getConditions()){
            $policy['conditions'] = $conditions;
        }
        return base64_encode(json_encode($policy));
    }

    /**
     * 获取许可条件
     *
     * @return array
     */
    protected function getConditions(){
        $conditions[] = [
            'bucket' => $this->bucket,
        ];
        if($this->fileMinLength > 0 && $this->fileMaxLength > 0 && $this->fileMaxLength > $this->fileMinLength){
            $conditions[] = [
                'content-length-range',
                $this->fileMinLength,
                $this->fileMaxLength,
            ];
        }
        if($this->validationType == self::STARTSWITH_VALIDATION && $prefix = $this->getFilePrefix()){
            $conditions[] = [
                'starts-with',
                '$key',
                $prefix,
            ];
        }elseif($this->validationType == self::STRICT_VALIDATION){
            $conditions[] = [
                'eq',
                '$key',
                current($this->getFileNames()),
            ];
        }
        return $conditions;
    }

    /**
     * 获取许可过期时间
     *
     * @return string
     */
    protected function getExpiration(){
        $end = $this->getEndTime();
        return $this->gmtIso8601($end);
    }

    /**
     * 获取unix过期时间
     *
     * @return string
     */
    protected function getEndTime(){
        return Yii::$app->time->unixTime + $this->expire;
    }

    /**
     * 转换时间格式
     *
     * @return string
     */
    protected function gmtIso8601($time){
        $dtStr = date('c', $time);
        $expiration = (new DateTime($dtStr))->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . 'Z';
    }
}
