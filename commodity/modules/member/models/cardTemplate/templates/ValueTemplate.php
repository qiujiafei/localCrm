<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\cardTemplate\templates;

use commodity\activeRecord\MemberTemplateValueAR;
use yii\data\ActiveDataProvider;

/**
 * Abstract role
 */
class ValueTemplate extends AbstractTemplate implements TemplateInterface
{
    public function generate($params){
        $template = MemberTemplateValueAR::find()->where(['name'=>$params['name']])->one();
        if ($template){
            throw new \Exception('卡模板名字重复', 2005);
        }else{
            $template = new MemberTemplateValueAR();
        }
        $template = self::loadModel($template,$params);
        return $template->save();
    }

    public function getOne($id){
        $template = MemberTemplateValueAR::findOne($id);
        return $template->attributes??[];
    }

    public function getAll($column,$condition,$countPerPage,$pageNum){
        return new ActiveDataProvider([
            'query' => MemberTemplateValueAR::find()->select($column)
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

}