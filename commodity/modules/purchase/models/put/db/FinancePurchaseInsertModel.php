<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/22
 * Time: 14:26
 */

namespace commodity\modules\purchase\models\put\db;

use commodity\modules\commoditybatch\models\get\db\Select;
use common\ActiveRecord\FinancePurchaseAR;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class FinancePurchaseInsertModel extends FinancePurchaseAR
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['last_modified_time','created_time'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * 批量存储数据
     * @param array $values
     * @return int
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public static function batchInsert($values=[])
    {
        //获取表所有字段
        $columns = self::getTableSchema()->getColumnNames();
        $data = self::resolveInsertColumns($columns,$values);
        $db = self::getDb();
        $sql = $db->getQueryBuilder()->batchInsert(self::tableName(),$columns,$data);

        return $db->createCommand( $sql )->execute();
    }

    /**
     * 为将要批量插入的字段进行赋值并对应相关位置
     * @param array $columns
     * @param array $values
     * @return array
     */
    private static function resolveInsertColumns($columns = [], $values = [])
    {
        $return = $temp = [];
        foreach ($values as $data) {
            foreach ($columns as $column) {
                if (isset($data[$column])) {
                    $temp[] = $data[$column];
                } else {
                    $temp[] = '';
                }
            }
            $return[] = $temp;
            unset($temp);
        }
        return $return;
    }


    /**
     * 为新插入的值设定默认值，该方法不可被直接调用，运用在特定场景下
     * @param array $commodities   商品信息
     * @param $purchases      采购信息
     * @return array
     */
    public static function createInsertDataOfPurchase($commodities=[],$purchases)
    {
        $return = [];
        $purchaseNumber = $purchases['number'];
        //获取采购批次入库编号，目前是入库id
        $batches = Select::getDataByPurchaseNumber($purchaseNumber,['id','commodity_id','cost_price']);

        $batchArray = $temp = [];
        $date = date('Y-m-d H:i:s');
        //仓库ID，用于过滤同一商品不同仓库的情况
        $depotCommodity = [];
        $tempDepot = [];
        $isMoreDepot = false;
        foreach($batches as $model) {
            $batchArray = $model->toArray();

            foreach($commodities as $key => $commodity) {

                if ($commodity['commodity_id'] == $batchArray['commodity_id']) {

                    $tempDepot = [
                        'commodity_id' => $commodity['commodity_id'],
                        'depot_id' => $commodity['depot_id'],
                        'batch_id' => $batchArray['id']
                    ];

                    //过滤同一商品，不同仓库问题
                    foreach ($depotCommodity as $depot) {
                        if ( ! array_diff_assoc($tempDepot,$depot)) {
                            $isMoreDepot = true;
                            continue;
                        }
                    }


                    if ($isMoreDepot){
                        break;
                    } else {
                        //追加仓库
                        $depotCommodity[] = $tempDepot;
                        $isMoreDepot = false;
                    }




                    $temp['commodity_id'] = $batchArray['commodity_id'];
                    //库存批次ID
                    $temp['commodity_batch_id'] = $batchArray['id'];
                    //该批次价格
                    $temp['commodity_batch_price'] = $commodity['total_price'];
                    $temp['status'] = 1;
                    $temp['comment'] = '采购';
                    $temp['created_by'] = $purchases['purchase_by'];
                    $temp['last_modified_by'] = $purchases['purchase_by'];
                    $temp['created_time'] = $date;
                    $temp['last_modified_time'] = $date;

                    $return[] = $temp;

                }
            }
        }

        //过滤重复，解决因为同批次的问题导致的问题
        $batchCommodity = [];
        $temp = [];
        foreach($return as $key => $value) {
            $temp = [
                'commodity_id' => $value['commodity_id'],
                'commodity_batch_id' => $value['commodity_batch_id']
            ];
            foreach ($batchCommodity as $bc) {
                if ( ! array_diff_assoc($temp,$bc)) {
                    unset($return[$key]);
                }
            }
            $batchCommodity[] = $temp;
        }

        return $return;
    }
}