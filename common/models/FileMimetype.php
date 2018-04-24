<?php
namespace common\models;

use Yii;
use common\ActiveRecord\FileMimetypeAR;
use common\models\RapidQuery;

class FileMimetype{

    /**
     * 获取mimetype的主键ID
     *
     * @return integer|null
     */
    public static function getId($mimetypeName, $createIfNotExist = false){
        $mimetypeName = strtolower($mimetypeName);
        $mimetypeId = (new RapidQuery(new FileMimetypeAR))->scalar([
            'select' => ['id'],
            'where' => ['name' => $mimetypeName],
        ]);
        if($mimetypeId){
            return $mimetypeId;
        }else if($createIfNotExist){
            return self::create($mimetypeName);
        }else{
            return null;
        }
    }

    /**
     * 创建mimetype
     *
     * @return integer|false
     */
    public static function create($mimetypeName){
        $affectedRow = (new RapidQuery(new FileMimetypeAR))->insert([
            'name' => $mimetypeName,
        ]);
        if($affectedRow){
            return Yii::$app->db->getLastInsertID();
        }else{
            return false;
        }
    }
}
