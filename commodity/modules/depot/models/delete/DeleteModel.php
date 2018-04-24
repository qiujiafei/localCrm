<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/10
 * Time: 15:10
 */

namespace commodity\modules\depot\models\delete;
use commodity\modules\commoditybatch\models\CommodityBatchObject;
use commodity\modules\depot\models\DepotObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\depot\models\get\db\ExistsModel;

class DeleteModel extends CommonModel
{
    const ACTION_INDEX = 'action_index';

    public $token;
    public $id;

    public function scenarios()
    {
        return [
            self::ACTION_INDEX => ['token','id']
        ];
    }

    /**
     * 删除仓库，只要仓库还有未完成的单据状态，不可删除。仓库只允许单个删除。
     * @return array|bool
     * @throws \Throwable
     */
    public function actionIndex()
    {
        try
        {
            $storeId = AccessTokenAuthentication::getUser(true);
            $depotObject = new DepotObject();
            if ( ! $depotObject->isExistsDepotIdOfStore($storeId,$this->id)) {
                throw new \Exception('仓库不存在',8004);
            }

            //确定是否允许删除，该仓库有商品库存，均不可删除。
            $commodityBatchObject = new CommodityBatchObject();
            if ($commodityBatchObject->isInStockOfDepot($storeId,$this->id)){
                throw new \Exception('仓库不允许删除',8007);
            }
            if ($depotObject->deleteById($this->id)) {
                return [];
            }
            throw new \Exception('删除失败',8008);
        }
        catch (\Exception $e)
        {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

    }
}