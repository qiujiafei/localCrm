<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 17:13
 */

namespace commodity\modules\depot\models\modify\db;
use common\ActiveRecord\DepotAR;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;
use commodity\modules\commodityManage\models\modify\ModifyDepotName;

class UpdateModel extends DepotAR
{
    const EVENT_UPDATE_COMMODITY_DEPOT_NAME = 'update_commodity_depot_name';

    //是否修改仓库名称
    public $isUpdateDepotName = false;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['last_modified_time'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    public function updateDepotData()
    {
//        if ($this->isUpdateDepotName){
//            $this->trigger(self::EVENT_UPDATE_COMMODITY_DEPOT_NAME);
//            return;
//        }

        if ( ! $this->update()){
            throw new \Exception('操作失败',8002);
        }

    }

    public function eventUpdateDepotName($event)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try
        {
            if ( ! $this->update()){
                throw new \Exception('操作失败',8002);
            }
            $res = ModifyDepotName::renameDepotName( $this->getAttribute('depot_name'), $event->data['old_depot_name'],$this->getAttribute('store_id') );
            //更新产品仓库名，调用对外接口
            if ( ! $res) {
                throw new \Exception('商品库更新仓库名失败',8005);
            }

            $transaction->commit();
            return true;
        }
        catch (\Exception $e)
        {
            //0 表示对方有异常
            if ($e instanceof \common\exceptions\RuntimeException){
                throw new \Exception('商品库更新仓库名失败',8005);
                return false;
            }

            $transaction->rollBack();
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }
    }

    /**
     * 改变更新的值属性
     * @param array $attributes   传入的值
     * @param array $debarFields  排除不可更新的值，数组格式，每个元素值表示排除的字段
     */
    public function changeAttributes($attributes=[],$debarFields=[])
    {
        $debarFieldsFlip = array_flip($debarFields);
        foreach ($attributes as $name => $value){
            if (isset($debarFieldsFlip[$name])){
                continue;
            }
            if (isset($attributes[$name])){
                $this->setAttribute($name,$attributes[$name]);
            }
        }
    }

}