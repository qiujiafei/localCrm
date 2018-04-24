<?php
namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class FileMimetypeAR extends ActiveRecord{

    public static function tableName(){
        return '{{%file_mimetype}}';
    }
}
