<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/16
 * Time: 14:46
 * 作废盘点单据
 */

namespace commodity\modules\inventory\interfaces;

use commodity\modules\commodityManage\models\interfaces\InventoryCheckInterface;
use commodity\modules\inventory\models\get\db\InventoryCommoditySelectModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class InventoryInvalid implements InventoryCheckInterface
{
    public function hasInventory($commodityId)
    {
        if ( ! is_numeric($commodityId)) {
            throw new \Exception('商品ID不合法',2004);
        }

        $storeId = AccessTokenAuthentication::getUser(true);

        $query = (new InventoryCommoditySelectModel())->isInvalidByCommodityIdOfInventory($commodityId,$storeId);

        foreach ($query as $model) {
            if (null === $model){
                continue;
            }
            //只要该值存在，表示有与之关联并作废的单据
            if ($model->inventories) {
                return true;
            }
        }

        return false;
    }
}