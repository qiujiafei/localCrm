<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 11:04
 */

namespace commodity\modules\supplier\models\put\db;

use common\ActiveRecord\SupplierAR;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class InsertModel extends SupplierAR
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_time','last_modified_time'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }
}