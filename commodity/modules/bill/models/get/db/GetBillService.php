<?php

/* * 
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\bill\models\get\db;

use common\ActiveRecord\BillAR;
use common\ActiveRecord\ServiceAR;
use yii\data\ActiveDataProvider;
use common\ActiveRecord\BillServiceAR;
use Yii;
use yii\data\Pagination;

class GetBillService extends BillServiceAR {

    private $pagination;

    public static function BillServiceName(array $condition) {

        return self::find()
                    ->select([
                        'b.service_name',
                    ])
                    ->from('crm_bill_service as a')
                    ->join('LEFT JOIN', 'crm_service As b', 'a.service_id = b.id')
                    ->where($condition)
                    ->asArray()
                    ->all();
    }


    public function getStatisticsList($storeId,$startTime,$endTime,$pageSize=20)
    {
        $where = [
            'and',
            ['b.store_id' => $storeId],
            ['between','bs.created_time',$startTime,$endTime]
        ];

        $query = self::find()
            ->alias('bs')
            ->select('bs.*')
            ->where($where)
            ->leftJoin('{{%bill}} b','b.id = bs.bill_id');

        //获取分页
        $count = $query->select('bs.service_id')
            ->groupBy('bs.service_id')
            ->count();

        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);
        $this->pagination = $pagination;
        //select字段清空
        $query->select = null;
        $query->groupBy = null;

        $query = $query
            ->limit($pagination->limit)
            ->offset($pagination->offset)
            ->with('service')
            ->with('bill');

        return $query;
    }

    public function getService()
    {
        return self::hasOne(ServiceAR::className(),['id' => 'service_id']);
    }

    public function getBill()
    {
        return self::hasMany(BillAR::className(),['id' => 'bill_id']);
    }

    public function getPagination()
    {
        return $this->pagination;
    }
}
