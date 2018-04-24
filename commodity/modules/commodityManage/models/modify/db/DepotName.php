<?php

namespace commodity\modules\commodityManage\models\modify\db;

use common\ActiveRecord\CommodityAR;
use common\exceptions;
use commodity\models\build\DepotBuildModel;
use Yii;

class DepotName
{
    public function __invoke($name, $originName)
    {
        if(empty($name) || empty($originName)) {
            throw new exceptions\InvalidArgumentException(
                sprintf('Name and OriginName must not be null. In %s.', __METHOD__)
            );
        }

        $db = Yii::$app->db;

        $tableName = (new CommodityAR)->getTableSchema()->fullName; 
        $command = $db->createCommand(
            "UPDATE {$tableName} SET default_depot_id = :name WHERE default_depot_id = :origin_name", 
            ['name' => $name, ':origin_name' => $originName]
        );

        if(!$command->execute()) {
            throw new exceptions\RuntimeException(
                sprintf('Cannot update depotname into Commodity table. In %s.', __METHOD__)
            );
        }

        return true;
    }
}
