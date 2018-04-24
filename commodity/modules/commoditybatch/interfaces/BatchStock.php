<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/16
 * Time: 14:27
 *
 * 批次库存检查
 */
namespace commodity\modules\commoditybatch\interfaces;
use commodity\modules\commoditybatch\models\get\db\Select;
use commodity\modules\commodityManage\models\interfaces\BatchCheckInterface;
use common\components\tokenAuthentication\AccessTokenAuthentication;

class BatchStock implements BatchCheckInterface
{
    /**
     * 通过商品ID去查询该商品是否还有库存
     * @param int $commodityId
     * @return bool
     * @throws \Exception
     */
    public function getBatchIsZero($commodityId)
    {
        if ( ! is_numeric($commodityId)) {
            throw new \Exception('商品ID不合法',2004);
        }
        //检查该商品ID是否存在

        $storeId = AccessTokenAuthentication::getUser(true);

        return (new Select())->getBatchStockByCommodityIdOfStore($commodityId,$storeId) ? false : true;
    }
}
