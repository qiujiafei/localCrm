<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/24
 * Time: 15:41
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\finance\models\get\db;

use commodity\modules\bill\models\get\db\GetBillService;
use common\ActiveRecord\BillAR;
use common\ActiveRecord\BillServiceAR;
use common\ActiveRecord\CustomerInfomationAR;
use common\ActiveRecord\ServiceAR;
use yii\data\Pagination;
use yii\db\ActiveQuery;

/**
 * 开单，这里用于统计客户到店次数，每开单一次，表示客户到达一次。
 * Class BillSelectModel
 * @package commodity\modules\finance\models\get\db
 */
class BillSelectModel extends BillAR
{
    /**
     * 获取符合条件的所有集合
     * @param $where   其中的like为日期阶梯模糊查询
     * @return ActiveQuery
     */
    public function getStatisticsOfMonthQuery($where)
    {
        $query = self::find()
            ->select(['created_time'])
            ->where(['store_id' => $where['store_id']])
            ->andWhere($where['like']);

        return $query;
    }

    /**
     * 创建返回值
     * @param $monthLadder
     * @param $where
     * @return array
     */
    public function createStatisticsDataOfMonth($monthLadder,$where)
    {
        //重建该数组，并赋值为0;
        $monthLadders = array_combine($monthLadder,array_fill(0,count($monthLadder),0));

        $where['like'] = $this->createMonthsLadderLike($monthLadder);
        //获取查询对象
        $query = $this->getStatisticsOfMonthQuery($where);
        $data = [];
        $Ym = '';
        //循环数据进行统计计算
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                $data = $model->toArray();
                $Ym = substr($data['created_time'],0,7);
                if (isset($monthLadders[$Ym])) {
                    $monthLadders[$Ym] += 1;
                }
            }
        }

        return $this->formatData($monthLadders);
    }

    /**
     * 格式化返回数据
     * @param array $data
     * @return mixed
     */
    private function formatData($data=[])
    {
        //月度统计
        $return['months'] = $data;
        //总次数
        $return['total'] = array_sum($data);
        return $return;
    }


    /**
     * 创建月份阶梯查询条件
     * @param $monthLadder
     * @return array
     */
    private function createMonthsLadderLike($monthLadder)
    {
        $return = [];
        foreach ($monthLadder as $month) {
            $return[] = ['like','created_time',$month.'%',false];
        }

        array_unshift($return,'or');
        return $return;
    }

    /**
     * 客户到店次数详情列表，每开单一次，便算作到店一次
     * @param $storeId
     * @param $startTime
     * @param $endTime
     * @param int $pageSize
     * @return array
     */
    public function findListByFrequencyOfStore($storeId,$startTime,$endTime,$pageSize=20)
    {
        $where = [
            'and',
            ['b.store_id' => $storeId],
            ['between','b.created_time',$startTime,$endTime]
        ];

        $query = self::find()
            ->alias('b')
            ->where($where);
        //获取分页数
        $count = $query->select(' date_format(`b`.`created_time`,\'%Y-%m-%d\') `created_times` ')->groupBy('created_times')->count();

        $query->select = null;
        $query->groupBy = null;

        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $query = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('b.id DESC');

        $temp = [];
        $list = [];
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                $data = $model->toArray();
                $Ymd = substr($data['created_time'],0,10);
                $temp['date'] = $Ymd;
                if (isset($list[$Ymd])) {
                    $temp = $list[$Ymd];
                    $temp['number_member'] = $list[$Ymd]['number_member'];
                    $temp['non_member'] = $list[$Ymd]['non_member'];
                } else {
                    $temp['number_member'] = 0;
                    $temp['non_member'] = 0;
                }
                //会员数统计,以消费时会员状态
                if ($model->is_member) {
                    $temp['number_member'] += 1;
                } else {
                    $temp['non_member'] += 1;
                }

                //总数
                $temp['total_number'] = $temp['number_member'] + $temp['non_member'];
                //会员消费占比
                $temp['membership_consumption_ratio'] = $temp['total_number']== 0 ? '0.00%' :round($temp['number_member'] / $temp['total_number'] * 100,2) .'%';
                //非会员消费占比
                $temp['non_membership_consumption_ratio'] = $temp['total_number'] == 0 ? '0.00%' :round($temp['non_member'] / $temp['total_number'] * 100,2) .'%';

                $list[$Ymd] = $temp;
            }
        }
        //排序，转换成索引数组
        sort($list);
        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];
    }

    /**
     * 客户信息
     *
     */
    public function getCustomerInformationOfIsMember()
    {
        return $this->hasOne(CustomerInfomationAR::className(),['id' => 'customer_infomation_id']);
    }

    /**
     * 营业额统计明细，现在只统计服务项目的营业额
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public function getListByService($where,$pageSize=20)
    {
        $query = self::find()
            ->alias('b')
            ->where(['b.store_id' => $where['store_id']])
            ->andFilterWhere(['between','b.created_time',$where['between'][0],$where['between'][1]]);

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $query = $query
            ->with(
                ['service' => function($query) use ($where){
                    return $query->where(['store_id'=>$where['store_id'],'status'=>1]);
                }
            ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('b.id DESC');
        $serviceId = '';
        $temp = [];
        $list = [];
        //服务项目总营业额
        $total_price = 0.00;
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                $services = $model->service;

                if ($services) {
                    foreach($services as $service) {
                        $serviceId = $service->id;
                        if (isset($list[$serviceId])) {
                            $temp = $list[$serviceId];
                            //施工次数，等于开单服务项目数
                            $temp['construction_times'] += 1;

                        } else {
                            $temp['construction_times'] = 1;
                            $temp['service_name']  = $service->service_name;
                            $temp['price'] = $service->price;
                        }
                        //实际收款金额
                        $temp['actual_receipts'] = round($temp['construction_times'] * $temp['price'],2);
                        //总金额
                        $total_price += $temp['actual_receipts'];

                        $list[$serviceId] = $temp;
                    }
                }
            }
        }
        //计算占比
        array_walk($list,function(&$data,$id) use ($total_price){
            $data['ratio'] = round($data['actual_receipts'] / $total_price * 100,2) . '%';
            return $data;
        });
        //重排序，转换成索引
        sort($list);
        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount,
            'total_price' => round($total_price,2)
        ];
    }

    /**
     * 施工统计，现在只统计服务项目的统计
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public function getListByServiceStatistics($where,$pageSize=20)
    {

        $billService = new GetBillService();
        $query = $billService->getStatisticsList($where['store_id'],$where['between'][0],$where['between'][1]);
        $serviceId = '';
        $temp = [];
        $list = [];
        //服务项目总营业额
        $total_price = 0.00;
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                if (null === $model){
                    continue;
                }
                $serviceId = $model->service_id;

                if (isset($list[$serviceId])) {
                    $temp['construction_times'] = $list[$serviceId]['construction_times'] + $model->quantity;
                }else {
                    //次数
                    $temp['construction_times'] =  $model->quantity;
                }

                $temp['service_name']  = $model->service->service_name;
                $temp['price'] = $model->service->price;
                $list[$serviceId] = $temp;
            }
        }

        $total_construction_times = array_sum(array_column($list,'construction_times'));

        //计算占比
        array_walk($list,function(&$data,$id) use ($total_construction_times){
            $data['ratio'] = round($data['construction_times'] / ($total_construction_times ?? 1) * 100,2) . '%';
            return $data;
        });

        //重排序，转换成索引
        sort($list);

        return [
            'lists' => $list,
            'count' => $billService->getPagination()->pageSize,
            'total_count' => $billService->getPagination()->totalCount,
            'total_number' => round($total_construction_times,2)
        ];

        return $list;
    }


    /**
     * 获取开单已用服务列表
     */
    public function getService()
    {
        return $this->hasMany(ServiceAR::className(),['id'=>'service_id'])
            ->viaTable('{{%bill_service}}',['bill_id'=>'id']);
    }
}