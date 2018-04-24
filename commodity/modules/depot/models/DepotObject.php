<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/3/12
 * Time: 15:42
 */

namespace commodity\modules\depot\models;

use common\ActiveRecord\DepotAR;
use yii\base\UserException;
use yii\db\ActiveQuery;

class DepotObject
{
    //最终返回的数据
    private $_data;

    public function __construct()
    {
        $this->_data = null;
    }

    /**
     * 是否定义仓库名称
     * @param $depotName
     * @param null $db
     * @return bool
     * @throws UserException
     */
    public function isExistsDepotName($depotName,$db = null)
    {
        if ( ! $depotName) {
            throw new UserException('仓库名称必须',8011);
        }
        $where['depot_name'] = $depotName;
        return DepotAR::find()->where($where)->exists($db);
    }

    /**
     * 当前门店是否定义该仓库
     * @param $storeId
     * @param $depotName
     * @param null $db
     * @return bool
     * @throws UserException
     */
    public function isExistsDepotNameOfStore($storeId,$depotName,$db = null)
    {
        if ( ! $storeId) {
            throw new UserException('门店ID必须',8012);
        }

        if ( ! $depotName) {
            throw new UserException('仓库名称必须',8011);
        }

        $where = [
            'store_id' => $storeId,
            'depot_name' => $depotName
        ];

        return DepotAR::find()->where($where)->exists($db);
    }

    /**
     * 检测当前仓库ID是否属于当前门店
     * @param $storeId
     * @param $depotId
     * @param null $db
     * @return bool
     * @throws UserException
     */
    public function isExistsDepotIdOfStore($storeId,$depotId,$db = null)
    {
        if ( ! $storeId) {
            throw new UserException('门店ID必须',8012);
        }
        if ( ! $depotId) {
            throw new UserException('仓库ID必须',8013);
        }

        $where = [
            'id' => $depotId,
            'store_id' => $storeId
        ];

        return DepotAR::find()->where($where)->exists($db);
    }

    /**
     * 通过仓库ID或仓库名称，查询当前门店的仓库数据
     * @param $storeId
     * @param $depot
     * @return null|static
     * @throws UserException
     */
    public function getDepotOfStoreByIdOrName($storeId,$depot)
    {
        if ( ! $storeId) {
            throw new UserException('门店ID必须',8012);
        }

        $where['store_id'] = $storeId;
        if (is_numeric($depot)) {
            $where['id'] = $depot;
        } else {
            $where['depot_name'] = $depot;
        }

        return $this->_data = DepotAR::findOne($where);
    }

    /**
     * 获取仓库query对象
     * @param $where
     * @return \yii\db\ActiveQuery
     */
    public function getDepotQuery($where)
    {
        return DepotAR::find()->where($where);
    }

    /**
     * 获取列表数据
     * @param ActiveQuery $query
     * @param string $fields
     * @param int $offset
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getListByQuery(ActiveQuery $query,$fields = '*',$offset = 0,$limit = 20)
    {
        $this->_data = $query
                    ->select($fields)
                    ->offset($offset)
                    ->limit($limit)
                    ->orderBy('created_time DESC')
                    ->all();

        return $this->_data;
    }

    /**
     * 删除一条数据
     * @param $id  仓库ID
     * @return false|int
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteById($id)
    {
        if ( ! $id) {
            throw new UserException('仓库ID必须',8013);
        }
        return DepotAR::findOne($id)->delete();
    }

    /**
     * 对象数据转换成数组
     * @return array
     */
    public function toArray()
    {
        if (is_array($this->_data)) {
            foreach ($this->_data as $key => $model){
                if ($model instanceof DepotAR) {
                    $this->_data[$key] = $model->toArray();
                }
            }
            return $this->_data;
        }

        if ($this->_data instanceof DepotAR) {
            return $this->_data->toArray();
        }

        return (array) $this->_data;
    }
}