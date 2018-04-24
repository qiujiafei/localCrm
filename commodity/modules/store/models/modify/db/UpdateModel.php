<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/22
 * Time: 10:03
 */
namespace commodity\modules\store\models\modify\db;
use common\ActiveRecord\StoreAR;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class UpdateModel extends StoreAR
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_modified_time'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }
}