<?php

/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\inventory\models\put\db;

use common\ActiveRecord\InventoryAR;
use yii\behaviors\TimestampBehavior;


class InsertDB extends InventoryAR
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
