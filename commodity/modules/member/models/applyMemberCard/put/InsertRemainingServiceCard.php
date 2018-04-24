<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qiujiafei<qiujiafei@9daye.com.cn>
 */

namespace commodity\modules\member\models\applyMemberCard\put;



use commodity\activeRecord\MemberRemainingTimesServiceAR;
use commodity\activeRecord\MemberRemainingDiscountServiceAR;


use Yii;


class InsertRemainingServiceCard
{

    //添加
    public static function insertRemainingServiceCard(array $data,$serviceDetails) {


        //插入计次卡服务明细表
        if($data['member_card_type_id'] == 2){

            //整理需要插入的的字段
            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_times_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],

            );

            foreach($serviceDetails as $k=>&$v){
                $v=array_merge($v,$newData);

            }


            try{
                $insert = new MemberRemainingTimesServiceAR();
                foreach ($serviceDetails as $attributes){
                    $in = clone $insert;
                    $in -> setAttributes($attributes,false);
                    $in -> save();
                }
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }

        //插入折扣卡折扣明细表
        if($data['member_card_type_id'] == 3){

            //整理需要插入的的字段
            $newData = array(
                'customer_infomation_id' => $data['customer_infomation_id'],
                'member_template_discount_id'=>$data['member_template_id'],
                'store_id'=>$data['store_id'],
                'member_number'=>$data['member_number'],
            );

            foreach($serviceDetails as $k=>&$v){
                $v=array_merge($v,$newData);

            }

            try{
                $insert = new MemberRemainingDiscountServiceAR();
                foreach ($serviceDetails as $attributes){
                    $in = clone $insert;
                    $in -> setAttributes($attributes,false);
                    $in -> save();
                }
                return true;
            }catch (\Exception $ex){
                throw $ex;
            }
        }




    }




}
