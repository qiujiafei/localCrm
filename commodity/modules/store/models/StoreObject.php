<?php

namespace commodity\modules\store\models;

use common\ActiveRecord\StoreAR;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class StoreLogicModel
 * @name 鬼一浪人
 * @author hejinsong@9daye.com.cn
 * @description 门店相关逻辑
 * @package commodity\modules\store\models
 */
class StoreObject
{
    private $_data;

    //是主门店
    const IS_MAIN_STORE = 1;
    const IS_NOT_MAIN_STORE = 0;
    /**
     * @description 是否是总店
     * @param $storeId
     * @return bool
     */
    public function isMainStore($storeId)
    {
        $where = [
            'id' => $storeId,
            'is_main_store' => self::IS_MAIN_STORE
        ];
        return StoreAR::find()->where($where)->exists();
    }

    /**
     * @description  是否是分店
     * @param $storeId
     * @return int|string
     */
    public function isBranchStore($storeId)
    {
        $where = [
            'id' => $storeId,
            'is_main_store' => self::IS_NOT_MAIN_STORE,
        ];
        return StoreAR::find()->where($where)->exists();
    }

    /**
     * @description 是否有分店
     * @param $storeId  主门店
     * @return bool
     */
    public function isHaveBranchStore($storeId)
    {
        $where = [
            'parent_id' => $storeId,
            'is_main_store' => self::IS_NOT_MAIN_STORE
        ];
        return StoreAR::find()->where($where)->count() ? true : false;
    }

    /**
     * @description 是否属于我的分店
     * @param $storeId
     * @param $branchId
     * @return bool
     */
    public function isOwnerBranchStore($storeId,$branchId)
    {
        return in_array($branchId,$this->getBranchStoreIds($storeId));
    }

    /**
     * @description 查询时的条件，总店和非总店，各有不同
     * @param $storeId
     * @return array
     */
    public function createListWhereByStoreId($storeId)
    {
        $where = [];
        if ($this->isMainStore($storeId)){
            $where = ['or',['id' => $storeId],['parent_id' => $storeId]];
        } else {
            $where['id'] = $storeId;
        }
        return $where;
    }

    /**
     * @description 返回门店的所有分店ID
     * @param $storeId
     * @return array
     */
    public function getBranchStoreIds($storeId)
    {
        if ( ! $this->isHaveBranchStore($storeId)) {
            return [];
        }
        $where = [
            'parent_id' => $storeId,
            'is_main_store' => self::IS_NOT_MAIN_STORE
        ];
        $data = StoreAR::find()->select('id')->where($where)->asArray(true)->all();
        if (count($data)) {
            return array_column($data,'id');
        }
        return [];
    }

    /**
     * 通过门店ID获取数据
     * @param $storeId
     * @return \yii\db\ActiveQuery
     */
    public function getOneById($storeId)
    {
        $where['id'] = $storeId;
        return $this->_data = StoreAR::find()->where($where);
    }

    /**
     * 获取列表查询query对象
     * @param $where
     * @return \yii\db\ActiveQuery
     */
    public function findListQuery($where)
    {
        return StoreAR::find()->where($where);
    }

    /**
     * 获取分页数据
     * @param ActiveQuery $query
     * @param string $fields
     * @param int $offset
     * @param int $limit
     * @return array|ActiveRecord[]
     */
    public function findListQueryOfPagination(ActiveQuery $query,$fields = '*',$offset = 0,$limit = 20)
    {
        $this->_data = $query
            ->select($fields)
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_time DESC')
            ->all();

        return $this->_data;
    }

    public function toArray()
    {
        if ($this->_data instanceof ActiveQuery){
            $this->_data = $this->_data->all();
        }
        if (is_array($this->_data)) {
            foreach ($this->_data as $key => $model){
                if ($model instanceof ActiveRecord) {
                    $this->_data[$key] = $model->toArray();
                }
            }
            return $this->_data;
        }

        if ($this->_data instanceof ActiveRecord) {
            return $this->_data->toArray();
        }

        return (array) $this->_data;
    }
}