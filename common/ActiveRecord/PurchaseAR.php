<?php
/* *
 * CRM system for 9daye
 * 采购表AR模型
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;

class PurchaseAR extends  ActiveRecord
{
    public static function tableName()
    {
        return '{{%purchase}}';
    }
}