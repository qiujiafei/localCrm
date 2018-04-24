<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/24
 * Time: 15:07
 * @author 鬼一浪人 <hejinsong@9daye.com.cn>
 */

namespace commodity\modules\finance\models\get\db;

use common\ActiveRecord\CustomerInfomationAR;
use yii\db\ActiveQuery;

class CustomerInformationSelectModel extends CustomerInfomationAR
{
    /**
     * 获取符合条件的所有集合
     * @param $where   其中的like为日期阶梯模糊查询
     * @return ActiveQuery
     */
    public function getStatisticsOfMonthQuery($where)
    {
        $query = self::find()
            ->select('is_member,created_time')
            ->where($where);

        return $query;
    }

    /**
     * 创建返回值
     * @param $monthLadder
     * @param $storeId
     * @return array
     */
    public function createStatisticsDataOfMonth($monthLadder,$storeId)
    {
        //重建该数组，并赋值为0;
        $monthLadders = array_combine($monthLadder,array_fill(0,count($monthLadder),0));
        $where = [
            'and',
            ['store_id' => $storeId],
            //只查询正常的用户
            ['status' => 1]
        ];
        $where[] = $this->createMonthsLadderLike($monthLadder);
        //获取查询对象
        $query = $this->getStatisticsOfMonthQuery($where);
        $data = [];
        $Ym = '';
        $member = $notMember = 0;
        //循环数据进行统计计算
        foreach ($query->batch(100) as $rows) {
            foreach ($rows as $model) {
                $data = $model->toArray();
                $Ym = substr($data['created_time'],0,7);
                if (isset($monthLadders[$Ym])) {
                    $monthLadders[$Ym] += 1;
                    //会员
                    $member += $data['is_member'] ? 1 : 0;
                    //非会员
                    $notMember += $data['is_member'] ? 0 : 1;
                }
            }
        }
        $memberInfo['member'] = $member;
        $memberInfo['non_member'] = $notMember;

        return $this->formatData($monthLadders,$memberInfo);
    }

    /**
     * 格式化返回数据
     * @param array $data
     * @param array $memberInfo  用户其他信息
     * @return mixed
     */
    private function formatData($data=[],$memberInfo=[])
    {
        //月度统计
        $return['months'] = $data;
        $return = array_merge($return,$memberInfo);
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
}