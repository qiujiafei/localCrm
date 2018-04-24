<?php
/**
 * CRM system for 9daye
 *
 * @author: zhuangzhuang <qiujiafei@9daye.com.cn>
 */

namespace commodity\modules\member\models\memberCardType\get;


use commodity\activeRecord\MemberCardAR;
use Yii;
use yii\db\Query;

class SelectType {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;



    //获取对应卡种缩写
    public static function getMemberCardAcronym($cardTypedId) {

        return (new Query())->select('acronym')
                            ->from('crm_member_card_type')
                            ->where(['type'=>$cardTypedId])
                            ->one();
    }


    //获取所有卡种type
    public static function getCardTypeAll(){
        return (new Query()) ->select('type')
                             ->from('crm_member_card_type')
                             ->all();
     //return MemberCardTypeAR::find()->select('type')->asArray()->all();
    }


}