<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\commoditybatch\models\get\db;

use common\ActiveRecord\CommodityBatchAR;
use yii\data\ActiveDataProvider;
use Yii;

class Select extends CommodityBatchAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getone($id) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'id',
                            'unit_name',
                            'store_id',
                            'created_time',
                        ])
                        ->from('crm_unit')
                        ->where([
                            'id' => $id,
                            'store_id' => current(self::getUser())->store_id,
                        ])
                        ->asArray()
                        ->one();
    }

    public static function getall($count_per_page, $page_num, $condition) {

        self::verifyStoreId();

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()->select([
                        'a.id',
                        'a.supplier_id',
                        'd.type', //来源
                        'a.purchase_number',
                        'a.commodity_id',
                        'b.commodity_name',
                        'b.specification',
                        'b.commodity_code',
                        'a.depot_id',
                        'b.classification_id',
                        'b.classification_name',
                        'c.depot_name',
                        'b.commodity_code',
                        'a.stock',
                        'b.unit_id',
                        'b.unit_name',
                        'a.cost_price as price',
                        'a.created_time',
                    ])
                    ->from('crm_commodity_batch As a')
                    ->join('LEFT JOIN', 'crm_commodity As b', 'a.commodity_id = b.id')
                    ->join('LEFT JOIN', 'crm_depot As c', 'c.id = a.depot_id')
                    ->join('LEFT JOIN', 'crm_supplier As d', 'd.id = a.supplier_id')
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

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

    public static function verifyStoreId() {

        $user = current(self::getUser());

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }
    }

    /**
     * 通过采购单号获取当前批次所有信息
     * @param $purchaseNumber
     * @param string $fields
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDataByPurchaseNumber($purchaseNumber,$fields='*')
    {
        $where = ['purchase_number'=>$purchaseNumber];
        return self::find()->where($where)->select($fields)->all();
    }

    /**
     * 获取当前门店该商品的库存量
     * @param $commodityId
     * @param $storeId
     * @return mixed
     */
    public function getBatchStockByCommodityIdOfStore($commodityId,$storeId)
    {
        $where = [
            'store_id' => $storeId,
            'commodity_id' => $commodityId
        ];

        return self::find()->where($where)->sum('stock');
    }
}
