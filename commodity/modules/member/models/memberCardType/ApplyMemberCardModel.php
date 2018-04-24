<?php
/**
 * CRM system for 9daye
 *
 * @author: qzh <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\applyMemberCard;

use commodity\modules\member\models\applyMemberCard\put\InsertMemberPointCard;
use commodity\modules\member\models\applyMemberCard\put\InsertMemberPointLogCard;
use common\exceptions\Exception;
use common\models\Model as CommonModel;
use commodity\modules\member\models\applyMemberCard\get\Select;
use commodity\modules\member\models\applyMemberCard\put\InsertMemberCard;
use commodity\modules\member\models\applyMemberCard\put\InsertRemainingCard;
use commodity\modules\member\models\applyMemberCard\put\InsertRemainingLogCard;
use commodity\modules\member\models\applyMemberCard\put\InsertRemainingServiceCard;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class ApplyMemberCardModel extends CommonModel {

    const ACTION_APPLY_MEMBER_CARD = 'action_applymembercard';

    public $token;
    public $count_per_page;
    public $page_num;
    public $member_card_type_id;
    public $customer_infomation_id;
    public $member_template_id;
    public $member_comment;
    public $point;
    //public $recharge_money;
    //public $give_money;

    public function scenarios() {
        return [
            self::ACTION_APPLY_MEMBER_CARD => [
                'member_card_type_id',
                'customer_infomation_id',
                'member_template_id',
                'member_comment',
                'point'
            ],
            //self::ACTION_GET_ALL => ['token', 'keyword','status'],
        ];
    }

    public function rules() {
        return [
            [
                ['member_card_type_id','customer_infomation_id','member_template_id','point'],
                'required',
                'message' => 2004,
            ],
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
        ];
    }

    public function actionApplymembercard() {
        $user = AccessTokenAuthentication::getUser();


        $result = Select::getStoreCardStatus($this->member_card_type_id,$user['store_id']);

        try {
            if($result['status'] == "0"){
                throw new \Exception('您的门店已停用此卡种',21000);
            }
            if(!$result){
                throw new \Exception('您的门店还未设置此卡种',21001);
            }if($result == '卡种错误'){
                throw new \Exception('卡种参数错误',21002);
            }

        } catch (\Exception $e) {
            $this->addError($e->getMessage(),$e->getCode());
            return false;
        }

        if($result['status'] == 1){
            $post = Yii::$app->request->post();

            $data = array(
                'member_number' => self::getMemberNumber(8,$post),
                'store_id' => $user['store_id'],
                'created_by' => $user['id'],
                'total_times' => '',
                'created_time' => date("Y:m:d H:i:s",time()),
            );


            $data = array_merge($data,$post);//组合全部参数


            if($data['member_card_type_id'] == 2){

                $serviceDetails = Select::getTimesTemplateServiceAll($data['member_template_id'],$data['store_id']);//获取计次卡对应服务明细和次数

                foreach($serviceDetails as $k=>$v){

                    $data['total_times'] += $v['times'];
                }



            }

            if($data['member_card_type_id'] == 3){

                $serviceDetails = Select::getDiscountTemplateServiceAll($data['member_template_id'],$data['store_id']);//获取计次卡对应服务明细和次数


            }


            //开启事物
            $transaction = Yii::$app->db->beginTransaction();

            try{

                $InsertMemberCard=InsertMemberCard::insertMemberCard($data);
                if(!$InsertMemberCard){
                    throw new \Exception('会员基础卡信息表插入失败',21003);
                }

                $InsertRemainingCard=InsertRemainingCard::insertRemainingCard($data);
                if(!$InsertRemainingCard){
                    throw new \Exception('卡余量表插入失败',21004);
                }

                $InsertRemainingLogCard=InsertRemainingLogCard::insertRemainingLogCard($data);
                if(!$InsertRemainingLogCard){
                    throw new \Exception('余量日志表插入失败',21005);
                }

                if($data['member_card_type_id'] != 1){

                    $InsertRemainingServiceCard=InsertRemainingServiceCard::insertRemainingServiceCard($data,$serviceDetails);
                    if(!$InsertRemainingServiceCard){
                        throw new \Exception('会员服务明细表插入失败',21006);
                    }

                }


                $InsertMemberPointCard=InsertMemberPointCard::insertMemberPointCard($data);
                if(!$InsertMemberPointCard){
                    throw new \Exception('会员积分表插入失败',21007);
                }
                
                $InsertMemberPointLogCard=InsertMemberPointLogCard::insertMemberPointCard($data);
                if(!$InsertMemberPointLogCard){
                    throw new \Exception('会员积分日志表插入失败',21008);
                }

                $transaction->commit();
                return [];

            }catch (\Exception $e){
                $this->addError($e->getMessage(),$e->getCode());
                $transaction->rollBack();
                return false;
            }


        }
        return false;


    }


    //生成卡号
    public static function getMemberNumber($len = 8, array $data){

        $acronym = Select::getMemberCardAcronym($data['member_card_type_id']);

        $memberNumber =$acronym['acronym'].(random_int(pow(10, $len - 1), pow(10, $len) - 1));

        $condition = array('member_number'=>$memberNumber);

        $info = Select::getField($condition,'member_number');
        if($info){
            $memberNumber = self::getMemberNumber($len,$data);
        }
        return $memberNumber;
    }






}