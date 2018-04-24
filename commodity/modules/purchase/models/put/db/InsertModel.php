<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\models\put\db;

use common\ActiveRecord\PurchaseAR;
use yii\behaviors\TimestampBehavior;


class InsertModel extends PurchaseAR
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_time','last_modified_time'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }
}
