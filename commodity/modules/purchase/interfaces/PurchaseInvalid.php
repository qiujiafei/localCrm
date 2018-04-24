<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/16
 * Time: 15:08
 * 采购单作废
 */

namespace commodity\modules\purchase\interfaces;

use commodity\modules\commodityManage\models\interfaces\PurchaseCheckInterface;
use commodity\modules\purchase\models\get\db\PurchaseCommoditySelectModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class PurchaseInvalid implements PurchaseCheckInterface
{
    public function hasPurchase($commodityId)
    {
        if ( ! is_numeric($commodityId)) {
            throw new \Exception('商品ID不合法',2004);
        }

        $storeId = AccessTokenAuthentication::getUser(true);
        $query = (new PurchaseCommoditySelectModel())->isInvalidByCommodityIdOfPurchase($commodityId,$storeId);

        foreach ($query as $model) {
            if (null === $model){
                continue;
            }
            //只要该值存在，表示有与之关联并作废的单据
            if ($model->purchases) {
                return true;
            }
        }

        return false;
    }
}