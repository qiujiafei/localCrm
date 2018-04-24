<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/22
 * Time: 14:24
 */

namespace common\ActiveRecord;

use yii\db\ActiveRecord;
/**
 * Class FinancePurchaseAR
 * @package common\ActiveRecord
 * 采购财务数据
 */
class FinancePurchaseAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%finance_purchase}}';
    }
}