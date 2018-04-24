<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\cardTemplate\templates;

use commodity\activeRecord\MemberTemplateTimesAR;
use commodity\activeRecord\MemberTemplateTimesServiceAR;
use yii\data\ActiveDataProvider;

/**
 * Abstract role
 */
class TimesTemplate extends AbstractTemplate implements TemplateInterface
{
    public function generate($params){
        $template = MemberTemplateTimesAR::find()->where(['name'=>$params['name']])->one();
        if ($template){
            throw new \Exception('卡模板名字重复', 2005);
        }else{
            $template = new MemberTemplateTimesAR();
        }
        $template = self::loadModel($template,$params);
        return $template->save();
    }

    public function getOne($id){
        $template = MemberTemplateTimesAR::findOne($id);
        return $template->attributes??[];
    }

    public function getAll($column,$condition,$countPerPage,$pageNum){
        return new ActiveDataProvider([
            'query' => MemberTemplateTimesAR::find()->select($column)
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
        $insertColumn = ['member_template_times_id','store_id','service_id','times','comment'];
        $fixedItem = ['member_template_times_id'=>$id,'store_id'=>$storeId];
        $item = ['service_id'=>0,'times'=>0,'comment'=>''];
        $insertData = self::handleList($serviceList,$fixedItem,$item);
        return \Yii::$app->db->createCommand()
            ->batchInsert(MemberTemplateTimesServiceAR::tableName(),$insertColumn,$insertData)
            ->execute();
    }

    public function getService($id){
        $template =  self::getOne($id);
        if (!$template) {
            throw new \Exception('无法获取-1', 2005);
        }
        return MemberTemplateTimesServiceAR::find()
            ->where(['member_template_times_id'=>$template['id']])
            ->asArray()
            ->all();
    }
}