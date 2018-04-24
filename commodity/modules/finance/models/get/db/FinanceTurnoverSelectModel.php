<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/24
 * Time: 14:37
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */
namespace commodity\modules\finance\models\get\db;

use common\ActiveRecord\BillAR;
use common\ActiveRecord\FinanceTurnoverAR;
use commodity\modules\bill\models\get\db\SelectModel;
use yii\db\ActiveQuery;

class FinanceTurnoverSelectModel extends FinanceTurnoverAR
{
    /**
     * 获取符合条件的所有集合
     * @param $where 其中的like为营业额日期阶梯模糊查询
     * @return ActiveQuery
     */
    public function getStatisticsOfMonthQuery($where)
    {
        $query = self::find()->alias('ft')
            ->where(['b.store_id' => $where['store_id']])
            ->andWhere($where['like'])
            ->with('bill')
            ->leftJoin('{{%bill}} b','ft.bill_id = b.id')
            ->groupBy('ft.id');

        return $query;
    }

    /**
     * 创建返回值
     * @param $daysLadder
     * @param $where
     * @return array
     */
    public function createStatisticsDataOfMonth($daysLadder,$where)
    {
        //重建该数组，并赋值为0;
        $monthLadders = array_combine($daysLadder,array_fill(0,count($daysLadder),0));

        $where['like'] = $this->createMonthsLadderLike($daysLadder);
        //获取查询对象
        $query = $this->getStatisticsOfMonthQuery($where);

        $data = [];
        $Ymd = '';
        $billIdArr = [];
        //循环数据进行统计计算
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                if (null === $model) {
                    continue;
                }
                $data = $model->toArray();
                $Ymd = substr($data['created_time'],0,10);
                if (isset($monthLadders[$Ymd]) && ! in_array($model->bill_id,$billIdArr)) {
                    $monthLadders[$Ymd] += $model->bill->final_price;
                }

                $billIdArr[] = $model->bill_id;
            }
        }

        $return = $this->formatData($monthLadders);
        //营业总额
        $return['total'] = (new SelectModel()) ->getFinalPriceAllOfStore($where['store_id']);
        return $return;
    }

    /**
     * 格式化返回数据
     * @param array $data
     * @return mixed
     */
    private function formatData($data=[])
    {
        //日统计
        $return['days'] = array_map(function($price){
            return number_format($price,2);
        },$data);
        //总额
        //$return['total'] = number_format(array_sum($data),2);
        return $return;
    }


    /**
     * 创建月份阶梯查询条件
     * @param $daysLadder
     * @return array
     */
    private function createMonthsLadderLike($daysLadder)
    {
        $return = [];
        foreach ($daysLadder as $month) {
            $return[] = ['like','ft.created_time',$month.'%',false];
        }

        array_unshift($return,'or');
        return $return;
    }


    /**
     * 获取今日营业额
     * @param $where  store_id为当前门店，today为今日开始时间和结束时间
     * @return mixed|string
     */
    public function getTurnoverByToday($where)
    {
        $sum = self::find()->alias('ft')
            ->where(['b.store_id' => $where['store_id']])
            ->andWhere(['between','b.created_time',$where['today'][0],$where['today'][1]])
            ->leftJoin('{{%bill}} b','ft.bill_id = b.id')
            ->sum('b.final_price');

        return $sum ?? '0.00';
    }

    /**
     * 查询开单信息
     * @return ActiveQuery
     */
    public function getBill()
    {
        return self::hasOne(BillAR::className(),['id' => 'bill_id']);
    }
}