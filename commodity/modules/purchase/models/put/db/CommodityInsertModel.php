<?php
/**
 * CRM system for 9daye
 *
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\purchase\models\put\db;

use commodity\models\build\CommodityBuildModel;
use common\ActiveRecord\PurchaseCommodityAR;
use yii\behaviors\TimestampBehavior;
use commodity\models\build\DepotBuildModel;
use Yii;
use yii\helpers\ArrayHelper;

class CommodityInsertModel extends PurchaseCommodityAR
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

            [['purchase_id','last_purchase_price','current_price','total_price'
                ,'quantity','unit_id'
            ],'required','message'=>'字段必须'],
            ['status','default','value'=>'0','on'=>'','message'=>''],
            ['comment','default','value'=>'','on'=>'','message'=>''],
            //仓库ID
            ['depot_id','filter','filter' => [$this,'checkDepotId'] ],
            //门店ID
            ['store_id','filter','filter' => [$this,'checkStoreId']],
            //商品ID
            ['commodity_id','filter','filter'=> [$this,'checkCommodityId']]
        ];
    }

    public function validateFields($data)
    {
        $model = '';
        $temp = $tempRepeat = [];
        foreach ($data as $d) {
            $model = new self($d);
            if ( ! $model->validate()) {
                throw new \Exception('字段验证不成功',12009);
            }
            //过滤同一个商品不可多次存入同一仓库的问题
            $temp = [
                'depot_id' => $model->depot_id,
                'commodity_id' => $model->commodity_id
            ];

            foreach ($tempRepeat as $value){
                //差集为空，表示有重复的
                if (count(array_diff_assoc($temp,$value)) == 0) {
                    throw new \Exception('该商品不可重复存储到该仓库',12018);
                }
            }

            $tempRepeat[] = $temp;

            if ( ! $model->save() ) {
                throw new \Exception('保存失败',12017);
            }
        }
        return true;
    }

    /**
     * 回调检测仓库是否合法，可直接调用
     * @param $value  仓库ID
     * @return mixed
     * @throws \Exception
     */
    public function checkDepotId($value)
    {
        if ( ! DepotBuildModel::isValidDepotId($value) || empty($value)) {
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
        if (null === $value || empty($value)) {
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
}