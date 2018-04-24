<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 15:41
 */

namespace commodity\modules\supplier\models\modify\db;

use common\ActiveRecord\SupplierAR;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class UpdateModel extends SupplierAR
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