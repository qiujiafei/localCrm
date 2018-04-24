<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\classification\models\get\db;

use common\ActiveRecord\ClassificationAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends ClassificationAR {


    public static function getAll(array $condition = array(), $field = '*') {

        return ClassificationAR::find()->select($field)->where($condition)->asArray()->all();
    }
}
