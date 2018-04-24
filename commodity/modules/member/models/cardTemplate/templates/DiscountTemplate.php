<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\cardTemplate\templates;

use commodity\activeRecord\MemberTemplateDiscountAR;
use commodity\activeRecord\MemberTemplateDiscountServiceAR;
use yii\data\ActiveDataProvider;

/**
 * Abstract role
 */
class DiscountTemplate extends AbstractTemplate implements TemplateInterface
{
    public function generate($params){
        $template = MemberTemplateDiscountAR::find()->where(['name'=>$params['name']])->one();
        if ($template){
            throw new \Exception('卡模板名字重复', 2005);
        }else{
            $template = new MemberTemplateDiscountAR();
        }
        $template = self::loadModel($template,$params);
        return $template->save();
    }

    public function getOne($id){
        $template = MemberTemplateDiscountAR::findOne($id);
        return $template->attributes??[];
    }

    public function getAll($column,$condition,$countPerPage,$pageNum){
        return new ActiveDataProvider([
            'query' => MemberTemplateDiscountAR::find()->select($column)
                ->where($condition)
                ->asArray(),
            'pagination' => [
                'page' => $pageNum - 1,
                'pageSize' => $countPerPage,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_time' => SORT_DESC,
                ],
            ],
        ]);
    }

    public function setStatus($id){
        $template = self::getOne($id);
        if (!$template) {
            throw new \Exception('无法获取-1', 2005);
        }
        $template->status = $template->status>0?0:1;
        return $template->save();
    }

    public function putService($id, $serviceList){
        $template =  self::getOne($id);
        if (!$template) {
            throw new \Exception('无法获取-1', 2005);
        }
        $storeId = $template['store_id'];
        $insertColumn = ['member_template_discount_id','store_id','service_id','discount','comment'];
        $fixedItem = ['member_template_times_id'=>$id,'store_id'=>$storeId];
        $item = ['service_id'=>0,'discount'=>0,'comment'=>''];
        $insertData = self::handleList($serviceList,$fixedItem,$item);
        return \Yii::$app->db->createCommand()->batchInsert(MemberTemplateDiscountServiceAR::tableName(),$insertColumn,$insertData)->execute();
    }

    public function getService($id){
        $template =  self::getOne($id);
        if (!$template) {
            throw new \Exception('无法获取-1', 2005);
        }
        return MemberTemplateDiscountServiceAR::find()
            ->where(['member_template_discount_id'=>$template['id']])
            ->asArray()
            ->all();
    }
}