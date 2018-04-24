<?php
/**
 * 盘点单AR
 * CRM system for 9daye
 * Date: 2018/2/3
 * Time: 10:12
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace common\ActiveRecord;


use yii\db\ActiveRecord;

class InventoryCommodityAR extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%inventory_commodity}}';
    }

}