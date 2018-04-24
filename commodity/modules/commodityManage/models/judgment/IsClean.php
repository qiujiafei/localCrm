<?php

namespace commodity\modules\commodityManage\models\judgment;

use commodity\modules\commoditybatch\interfaces\BatchStock;
use commodity\modules\inventory\interfaces\InventoryInvalid;
use commodity\modules\purchase\interfaces\PurchaseInvalid;
use commodity\modules\damagedcommodity\models\interfaces\DamagedCommodityCheck;
use commodity\modules\pickingcommodity\models\interfaces\PickingCommodityCheck;
use common\exceptions\Exception;

class IsClean
{
    private $commodityId;

    public function __construct($commodityId)
    {
        if(! is_numeric($commodityId)) {
            throw new Exception('Commodity Id should be integer.');
        }
        $this->commodityId = $commodityId; 
    }

    public function __invoke()
    {
        if(
            (new BatchStock)->getBatchIsZero($this->commodityId) &&
            !(new InventoryInvalid)->hasInventory($this->commodityId) &&
            !(new PurchaseInvalid)->hasPurchase($this->commodityId) &&
            !(new DamagedCommodityCheck)->hasDamaged($this->commodityId) &&
            !(new PickingCommodityCheck)->hasPacking($this->commodityId)
        ) {
            return true;
        }

        return false;
    }
}
