<?php
/**
 * CRM system for 9daye
 *
 * @author: qzh <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\cardTemplate;

use commodity\modules\member\models\cardtemplate\templates\AbstractTemplate;
use common\models\Model as CommonModel;
use commodity\modules\member\models\cardTemplate\templates\TimesTemplate;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class TimesTemplateModel extends CommonModel
{

    const ACTION_GET_ONE = 'action_get_one';
    const ACTION_GET_ALL = 'action_get_all';
    const ACTION_PUT_ONE = 'action_put_one';
    const ACTION_SET_ONE = 'action_set_one';
    const ACTION_PUT_BATCH = 'action_put_batch';
    const ACTION_GET_BATCH = 'action_get_batch';

    public $token;
    public $count_per_page;
    public $page_num;
    public $keyword;
    public $store_id;
    public $id;
    public $name;
    public $recharge_money;
    public $service_list;
    public $comment;
    public $status;

    public function scenarios() {
        return [
            self::ACTION_GET_ONE => ['id'],
            self::ACTION_GET_ALL => ['keyword','status'],
            self::ACTION_SET_ONE => ['id'],
            self::ACTION_PUT_ONE => ['name','recharge_money','comment'],
            self::ACTION_PUT_BATCH => ['id','service_list'],
            self::ACTION_GET_BATCH => ['id'],
        ];
    }

    public function rules() {
        return [
            [
                ['id'], 'required', 'message' => 2004, 'on'=>[self::ACTION_GET_ONE, self::ACTION_SET_ONE, self::ACTION_PUT_BATCH, self::ACTION_GET_BATCH]
            ],
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'message' => 2004,
            ],
            ['name', 'string', 'message' => 2004],
            ['recharge_money', 'give_money'], 'required', 'message' => 2004, 'on'=>self::ACTION_PUT_ONE,
            ['recharge_money', 'string', 'length'=>[1,12], 'message' => 2004],
            ['comment', 'string', 'length'=>[0,30], 'message' => 2004],
            ['status', 'int', 'message' => 2004],
            ['status', 'default', 'value' => 1],
            ['service_list', 'required', 'message' => 2004, 'on'=>self::ACTION_PUT_BATCH],
        ];
    }

    public function actionGetOne() {
        try {
            $template = new AbstractTemplate();
            $template->loadTemplate(new TimesTemplate());
            $result = $template->getOne($this->id);
            if (!$result) {
                $this->addError('DiscountTemplateModel/getone',21006);
            }
        } catch (\Exception $e) {
            $this->addError('TimesTemplateModel/getone', $e->getCode());
            return false;
        }
    }

    public function actionGetAll() {
        try {
            $column = ['id','name','recharge_money','give_money','comment'];
            $condition = ['and'];
            if (!empty(trim($this->keyword))){
                array_push($condition, ['like', 'name', trim($this->keyword)]);
            }
            if (!empty($this->status)){
                array_push($condition, ['status'=>$this->status]);
            }else{
                array_push($condition, ['status'=>1]);
            }
            $template = new AbstractTemplate();
            $template->loadTemplate(new TimesTemplate());
            $result = $template->getAll($column,$condition,$this->count_per_page??10,$this->page_num??1);
        } catch (\Exception $e) {
            $this->addError('TimesTemplateModel/getall', $e->getCode());
            return false;
        }
        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'list' => $result->models,
        ];
    }

    public function actionPutOne() {
        try {
            $user = AccessTokenAuthentication::getUser();
            $params = [
                'name'=>$this->name,
                'recharge_money'=>$this->recharge_money,
                'comment'=>$this->comment,
                'store_id'=>$user['store_id'],
                'created_by'=>$user['id'],
                'created_time'=>date('Y-m-d H:i:s')
            ];
            $template = new AbstractTemplate();
            $template->loadTemplate(new TimesTemplate());
            if ($template->generate($params)){
                throw new \Exception('无法获取-1', 2005);
            }
        } catch (\Exception $e) {
            $this->addError('TimesTemplateModel/putone', $e->getCode());
            return false;
        }
        return true;
    }

    public function actionPutBatch() {
        try {
            $template = new AbstractTemplate();
            $template->loadTemplate(new TimesTemplate());
            if (!$template->putService($this->id,$this->service_list)){
                throw new \Exception('无法获取-1', 2005);
            }
        } catch (\Exception $e) {
            $this->addError('TimesTemplateModel/putbatch', $e->getCode());
            return false;
        }
        return true;
    }

    public function actionGetBatch() {
        try {
            $template = new AbstractTemplate();
            $template->loadTemplate(new TimesTemplate());
            $result = $template->getCheckService($this->id);
        } catch (\Exception $e) {
            $this->addError('TimesTemplateModel/putbatch', $e->getCode());
            return false;
        }
        return $result;
    }
}