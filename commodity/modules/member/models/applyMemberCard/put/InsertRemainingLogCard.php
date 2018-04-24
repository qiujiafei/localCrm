<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qiujiafei<qiujiafei@9daye.com.cn>
 */

namespace commodity\modules\member\models\applyMemberCard\put;



use commodity\activeRecord\MemberRemainingValueLogAR;
use commodity\activeRecord\MemberRemainingTimesLogAR;
use commodity\activeRecord\MemberRemainingDiscountLogAR;


use Yii;

class InsertRemainingLogCard
{

    //添加
    public static function insertRemainingLogCard(array $data) {

        //插入储值卡日志表
        if($data['member_card_type_id'] == 1){

            //整理需要插入的的字段
            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
                'action_by'=>$data['created_by'],
                'recharge_money'=>$data['recharge_money'],
                'give_money'=>$data['recharge_money'],
                'action_time'=>$data['created_time'],
                'action'=>'1',
                'member_comment'=>$data['member_comment']
            );

            try{
                $insert = new MemberRemainingValueLogAR();
                foreach ($newData as $k=>$v){
                    $insert->$k = $v;
                }
                $insert->save();
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }

        //插入计次卡日志表
        if($data['member_card_type_id'] == 2){

            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
                'action_by'=>$data['created_by'],
                'recharge_money'=>$data['recharge_money'],
                'total_times'=>$data['total_times'],
                'action_time'=>$data['created_time'],
                'action'=>'1',
                'member_comment'=>$data['member_comment']
            );

            try{
                $insert = new MemberRemainingTimesLogAR();
                foreach ($newData as $k=>$v){
                    $insert->$k = $v;
                }
                $insert->save();
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }

        //插入折扣卡日志表
        if($data['member_card_type_id'] == 3){

            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
                'action_by'=>$data['created_by'],
                'money'=>$data['recharge_money'],
                'action_time'=>$data['created_time'],
                'action'=>'1',
                'member_comment'=>$data['member_comment']
            );

            try{
                $insert = new MemberRemainingDiscountLogAR();
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
