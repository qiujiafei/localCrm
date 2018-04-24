<?php
namespace common\ActiveRecord;

use yii\db\ActiveRecord;
/**
 * Class FinanceTurnoverAR
 * @package common\ActiveRecord
 * 营业额数据
 */
class FinanceTurnoverAR extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%finance_turnover}}';
    }
}