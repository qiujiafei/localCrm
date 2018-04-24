<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:36
 */

namespace commodity\modules\depot\models\put\db;
use common\ActiveRecord\DepotAR;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class InsertModel extends DepotAR
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
            ],
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['status'],
                ],
                'value' => 1
            ],
        ];
    }
}