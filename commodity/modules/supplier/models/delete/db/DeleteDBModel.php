<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 17:40
 */

namespace commodity\modules\supplier\models\delete\db;

use commodity\modules\supplier\models\SupplierObject;
use common\ActiveRecord\SupplierAR;
use yii\helpers\StringHelper;

class DeleteDBModel extends SupplierAR
{
    private $storeId = null;
    private $pkIds = [];
    private $countIds;  //传入的id数量多少

    /**
     * 验证传入被删除ID是否均合法
     * @param $storeId
     * @param $pkIds
     * @return bool
     *
     */
    public function checkIsExistsIds($storeId,$pkIds)
    {
        $this->storeId = $storeId;
        $this->pkIds = $pkIds;
        $this->resolveIds();
        $where = [
            'store_id' => $this->storeId,
            'id' => $this->pkIds
        ];

        return (new SupplierObject())->getCount($where) == $this->countIds;
    }

    /**
     * 解析被删除ID，转换成数组
     */
    private function resolveIds()
    {
        if (is_string($this->pkIds)) {
            $this->pkIds = StringHelper::explode($this->pkIds,',',true,true);
        }
        $this->countIds = count($this->pkIds);
    }

    /**
     * 获取解析后的结果
     * @return array
     */
    public function getIds()
    {
        return $this->pkIds;
    }


}