<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/13
 * Time: 13:33
 */

namespace commodity\modules\supplier\models;
use common\ActiveRecord\SupplierAR;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class SupplierObject
{
    //表示九大爷，任何门店共享，其值为-1
    const JIU_STORE_ID = -1;
    //九大爷供应商ID，其值为-1
    const JIU_SUPPLIER_ID = -1;


    private $_data;

    public function __construct()
    {
        $this->_data = null;
    }

    /**
     * 该门店供应商名称是否已存在
     * @param $storeId  门店ID
     * @param $mainName 供应商名称
     * @param null $db
     * @return bool
     */
    public function isExistsMainNameByName($storeId,$mainName,$db = null)
    {
        $where = [
            'store_id' => $storeId,
            'main_name' => $mainName
        ];

        return SupplierAR::find()->where($where)->exists($db);
    }

    /**
     * 判断九大爷名称是否使用
     * @param $mainName
     * @param null $db
     * @return bool
     */
    public function isExistsMainNameByNine($mainName,$db = null)
    {
        $where = [
            'id' => self::JIU_STORE_ID,
            'main_name' => $mainName
        ];

        return SupplierAR::find()->where($where)->exists($db);
    }

    /**
     * 通过id确定当前门店是否合法
     * @param $storeId
     * @param $id
     * @param null $db
     * @return bool
     */
    public function isExistsSupplierIdById($storeId,$id,$db = null)
    {
        $where = [
            'id' => $id,
            'store_id' => $storeId,
        ];

        return SupplierAR::find()->where($where)->exists($db);
    }

    /**
     * 是否是门店供应商，九大爷默认为门店供应商
     * @param $storeId
     * @param $supplierId
     * @return bool
     */
    public function isSupplierOfStore($storeId,$supplierId)
    {
        return $this->isSupplierOfNineById($supplierId) || $this->isExistsSupplierIdById($storeId,$supplierId);
    }

    /**
     * 该供应商ID是否是九大爷
     * @param $supplierId
     * @return bool
     */
    public function isSupplierOfNineById($supplierId)
    {
        return $supplierId == self::JIU_SUPPLIER_ID;
    }


    /**
     * 获取九大爷供应商信息
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getSupplierNine()
    {
        return SupplierAR::find()->where(['id'=>self::JIU_SUPPLIER_ID])->one();
    }

    /**
     * 获取门店供应商列表Query
     * @param $storeId
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierOfStoreQueryByStoreId($storeId)
    {
        $where = [
            'store_id' => $storeId
        ];
        return SupplierAR::find()->where($where)->orWhere(['id'=>self::JIU_SUPPLIER_ID]);
    }

    /**
     * 通过供应商ID获取该对象
     * @param $id  供应商ID
     * @return \yii\db\ActiveQuery
     */
    public function getSupplierQueryById($id)
    {
        return SupplierAR::find()->where(['id'=>$id]);
    }

    /**
     * 获取门店所有供应商
     * @param $storeId
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getSuppliersOfStore($storeId)
    {
        return $this->_data = $this->getSupplierOfStoreQueryByStoreId($storeId)->all();
    }

    /**
     * @param $storeId
     * @param $searchWhere
     * @return \yii\db\ActiveRecord[]
     */
    public function getListSuppliersQuery($storeId,$searchWhere=[])
    {
        return $this->getSupplierOfStoreQueryByStoreId($storeId)->andWhere($searchWhere);
    }

    /**
     * 获取供应商分页数据
     * @param ActiveQuery $query
     * @param int $offset
     * @param int $limit
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public function getListSuppliersQueryPagination(ActiveQuery $query,$offset = 0, $limit = 20)
    {
        $this->_data = $query
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_time DESC')
            ->all();

        return $this->_data;
    }

    /**
     * 根据条件获取总数
     * @param $where
     * @return int|string
     */
    public function getCount($where)
    {
        return SupplierAR::find()->where($where)->count();
    }

    /**
     * 获取一条数据
     * @param array $where
     * @return $this
     */
    public function findOneByWhere($where=[])
    {
        return $this->_data = SupplierAR::find()->where($where);
    }

    /**
     * 数据转换成数组
     * @return array|null
     */
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