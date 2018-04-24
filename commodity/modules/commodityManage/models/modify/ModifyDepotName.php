<?php

namespace commodity\modules\commodityManage\models\modify;

use commodity\models\interfaces\CommodityDepotInterface;
use commodity\modules\commodityManage\models\modify\db\DepotName;
use common\components\tokenAuthentication\AccessTokenAuthentication as Token; 
use common\exceptions\Exception as UserException;

class ModifyDepotName implements CommodityDepotInterface
{
    /**
     *
     * 重要警告!
     *
     * 更新商品信息管理表,必须保证仓库名的合法性
     * 因为在事务中无法验证仓库名更改后的合法性.
     *
     * @param  $name string
     * @param  $originName string
     * @param  $storeId integer
     * @throws exceptions\InvalidArgumentException
     * @throws exceptions\RuntimeException
     * @return bool
     */
    public static function renameDepotName($name, $originName, $storeId)
    {
        if($storeId !== Token::getUser(true)) {
            return false;
        }

        try {
            $result = (new DepotName)($name, $originName);
        } catch(UserException $ex) {
            throw $ex;
        } catch(\Exception $ex) {
            return false;
        }

        return $result;
    } 
}
