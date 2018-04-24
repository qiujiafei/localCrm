<?php

/* * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\get\db;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityImageAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends CommodityAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getOneWithObject($id)
    {
        $storeId = AccessTokenAuthentication::getUser(true);

        if (!$storeId) {
            throw new \Exception(sprintf(
                    "User's store is not found In %s.", __METHOD__
            ));
        }
        
        return self::findOne(['id' => $id, 'store_id' => $storeId]);
    }

    public static function getone($commodityName, $barcode) {
        $user = AccessTokenAuthentication::getUser();

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }

        return self::find()->select([
            'commodity.id',
            'commodity.commodity_name',
            'commodity.specification', //must modifed
            'commodity.commodity_code',
            'commodity.classification_id',
            'commodity.classification_name',
            'commodity.price',
            'commodity.barcode',
            'commodity.unit_id',
            'commodity.unit_name', //must modified
            'commodity.commodity_property_name',
            'commodity.status',
            'originate.name as originate',
            'commodity.default_depot_id',
            'depot.depot_name',
            'commodity.comment',
            'commodity.store_id',
            'commodity.created_time',
        ])
        ->from('crm_commodity as commodity')
        ->join('LEFT JOIN', '{{%commodity_originate}} As originate', 'commodity.commodity_originate_id = originate.id')
        ->join('LEFT JOIN', '{{%depot}} As depot', 'commodity.default_depot_id = depot.id')
        ->where($condition)
        ->asArray()
        ->all();
    }

    public static function getall($count_per_page, $page_num, $condition) {

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                'commodity.id',
                'commodity.commodity_name',
                'commodity.specification', //must modifed
                'commodity.commodity_code',
                'commodity.classification_id',
                'commodity.classification_name',
                'commodity.price',
                'commodity.barcode',
                'commodity.unit_id',
                'commodity.unit_name', //must modified
                'commodity.commodity_property_name',
                'commodity.status',
                'originate.name as originate',
                'commodity.default_depot_id',
                'depot.depot_name',
                'commodity.comment',
                'commodity.store_id',
                'commodity.created_time',
            ])
            ->from('crm_commodity as commodity')
            ->join('LEFT JOIN', '{{%commodity_originate}} As originate', 'commodity.commodity_originate_id = originate.id')
            ->join('LEFT JOIN', '{{%depot}} As depot', 'commodity.default_depot_id = depot.id')
            ->where($condition)
            ->asArray(),
                'pagination' => [
                    'page' => $page_num - 1,
                    'pageSize' => $count_per_page,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'created_time' => SORT_DESC,
                    ],
                ],
            ]);
    }

    public static function getexport($condition) {

        return self::find()->select([
                            'id',
                            'commodity_name',
                            'specification', //must modifed
                            'commodity_code',
                            'classification_name',
                            'price',
                            'barcode',
                            'unit_name', //must modified
                            'commodity_property_name',
                            'status',
                            'commodity_originate_id as originate',
                            'default_depot_id',
                            'comment',
                            'store_id',
                            'created_time',
                        ])->where($condition)
                        ->asArray()
                        ->all();
    }

}
