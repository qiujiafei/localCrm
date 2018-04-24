<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author qiujiafei<qiujiafei@9daye.com.cn>
 */

namespace commodity\modules\member\models\applyMemberCard\put;



use commodity\activeRecord\MemberCardAR;

use Yii;

class InsertMemberCard extends MemberCardAR
{

    //添加
    public static function insertMemberCard(array $data) {

        //整理需要插入的的字段
        $newData = array(
            'customer_infomation_id' => $data['customer_infomation_id'],
            'member_card_type_id'=>$data['member_card_type_id'],
            'member_template_id'=>$data['member_template_id'],
            'store_id'=>$data['store_id'],
            'member_number'=>$data['member_number'],
            'created_by'=>$data['created_by'],
            'apply_card_time'=>$data['created_time'],
            'is_member'=>'1',
            'member_comment'=>$data['member_comment']
        );

        try{
            $insert = new self;
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
