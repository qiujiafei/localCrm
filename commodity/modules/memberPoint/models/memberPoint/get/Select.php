<?php
/**
 * CRM system for 9daye
 *
 * @author: wj <wangjie@9daye.com.cn>
 */

namespace commodity\modules\memberPoint\models\memberPoint\get;

use commodity\activeRecord\MemberPointAR;
use Yii;

class Select extends MemberPointAR
{
    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function getTotal($store_id)
    {
        return self::find()->select(['recharge_points', 'consumption_points'])
            ->where(['store_id' => $store_id])
            ->asArray()
            ->all();
    }

    public static function getMoonPoint($store_id, $begin, $end)
    {
        $con[0] = 'and';
        $con[1] = ['between','last_modified_time', $begin, $end];
        $con[2] = ['store_id' => $store_id];

        return self::find()->select(['recharge_points','consumption_points'])
            ->where($con)
            ->asArray()
            ->all();
    }

    public static function getDayPoint($store_id, $today_start, $today_end)
    {
        $cond[0] = 'and';
        $cond[1] = ['between', 'last_modified_time', $today_start, $today_end];
        $cond[2] = [ 'store_id' => $store_id];
        return self::find()->select(['recharge_points','consumption_points'])
            ->where($cond)
            ->asArray()
            ->all();
    }

    public static function getMemberInfo($store_id)
    {
        return self::find()->select('ci.customer_name,ci.gender,ci.cellphone_number,cc.number_plate_number,cc.frame_number,csh.`name`,mp.recharge_points,mp.consumption_points')
            ->from('crm_member_point as mp')
            ->join('LEFT JOIN','crm_customer_infomation as ci','mp.customer_infomation_id=ci.id')
            ->join('LEFT JOIN','crm_customer_cars as cc','ci.id=cc.customer_infomation_id')
            ->join('LEFT JOIN','crm_car_style_home as csh','cc.model_id=csh.id')
            ->where(['mp.store_id' => $store_id])
            ->orderBy('mp.last_modified_time DESC')
            ->asArray()
            ->all();
    }


}