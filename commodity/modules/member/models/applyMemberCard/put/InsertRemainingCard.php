<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qiujiafei<qiujiafei@9daye.com.cn>
 */

namespace commodity\modules\member\models\applyMemberCard\put;

use commodity\activeRecord\MemberRemainingValueAR;
use commodity\activeRecord\MemberRemainingTimesAR;
use commodity\activeRecord\MemberRemainingDiscountAR;


use Yii;

class InsertRemainingCard
{

    //添加
    public static function insertRemainingCard(array $data) {

        //插入储值卡余量表
        if($data['member_card_type_id'] == 1){

            //整理需要插入的的字段
            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
                'created_by'=>$data['created_by'],
                'recharge_money'=>$data['recharge_money'],
                'give_money'=>$data['recharge_money'],
                );

            try{
                $insert = new MemberRemainingValueAR();
                foreach ($newData as $k=>$v){
                    $insert->$k = $v;
                }
                $insert->save();
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }

        //插入计次卡余量表
        if($data['member_card_type_id'] == 2){

            //整理需要插入的的字段
            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
                'total_times'=>$data['total_times'],
            );

            try{
                $insert = new MemberRemainingTimesAR();
                foreach ($newData as $k=>$v){
                    $insert->$k = $v;
                }
                $insert->save();
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }

        //插入折扣卡余量表
        if($data['member_card_type_id'] == 3){

            //整理需要插入的的字段
            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
                'remaining_money'=>$data['recharge_money'],
            );

            try{
                $insert = new MemberRemainingDiscountAR();
                foreach ($newData as $k=>$v){
                    $insert->$k = $v;
                }
                $insert->save();
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }




    }




}
