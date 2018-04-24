<?php
/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\inventory\models\put\db;

use commodity\models\build\CommodityBuildModel;
use commodity\modules\inventory\logics\CommodityBatchLogicModel;
use common\ActiveRecord\InventoryCommodityAR;
use yii\behaviors\TimestampBehavior;
use commodity\models\build\DepotBuildModel;


class CommodityInsertModel extends InventoryCommodityAR
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_time','last_modified_time'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function rules()
    {
        return [

            [['commodity_id','depot_id'
                ,'quantity','unit_id','store_id'
            ],'required','message'=>'字段必须'],
            ['status','default','value'=>'0','on'=>'','message'=>''],
            ['comment','default','value'=>'','on'=>'','message'=>''],
            //仓库ID
            ['depot_id','filter','filter' => [$this,'checkDepotId'] ],
            //门店ID
            ['store_id','filter','filter' => [$this,'checkStoreId']],
            //商品ID
            ['commodity_id','filter','filter'=> [$this,'checkCommodityId']],
            //批次号验证
            ['commodity_batch_id','filter','filter' => [$this,'checkCommodityBatchId']]
        ];
    }

    public function validateFields($data)
    {
        $models = [];
        foreach ($data as $d) {
            $model = new self($d);
            $models[] = $model;
            if ( ! $model->save() ) {
                throw new \Exception('字段验证不成功',18009);
            }
        }
        return true;
    }

    /**
     * 回调检测仓库是否合法，可直接调用
     * @param $value  仓库ID
     * @return string
     * @throws \Exception
     */
    public function checkDepotId($value)
    {
        if ( ! DepotBuildModel::isValidDepotId($value)) {
            throw new \Exception('仓库未定义',8009);
        }
        return $value;
    }

    /**
     * 回调验证门店ID是否合法
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function checkStoreId($value)
    {
        if (null === $value) {
            throw new \Exception('门店ID不合法',11001);
        }
        return $value;
    }

    /**
     * 回调验证商品ID是否合法
     * @param $value
     * @return bool
     * @throws \Exception
     */
    public function checkCommodityId($value)
    {
        //验证商品ID是否合法
        if (null === $value || ! CommodityBuildModel::isValidIdOfStore($value)) {
            throw new \Exception('该商品不属于该商店或未定义',2011);
        }
        return $value;
    }

    /**
     * 检查批次号的合法性
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function checkCommodityBatchId($value)
    {
        if (null === $value || ! CommodityBatchLogicModel::isExistsIdOfStore($value))
        {
            throw new \Exception('批次号不合法',18008);
        }
        return $value;
    }
}