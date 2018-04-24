<?php

/* *
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\store\models\get;

use common\models\Model as CommonModel;
use admin\modules\store\models\get\db\Select;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETACCREDITALL = 'action_getaccreditall';
    const ACTION_GETFORBIDDENALL = 'action_getforbiddenall';

    public $count_per_page;
    public $page_num;
    public $property_id; //门店性质
    public $start_time; //开始时间
    public $end_time; //结束时间
    public $type; //1最后一次登录时间  2禁止时间
    public $account;

    public function scenarios() {
        return [
            self::ACTION_GETACCREDITALL => [
                'count_per_page', 'page_num', 'property_id', 'start_time', 'end_time', 'type', 'account'
            ],
            self::ACTION_GETFORBIDDENALL => [
                'count_per_page', 'page_num', 'property_id', 'start_time', 'end_time', 'type', 'account'
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
        ];
    }

    //获取授权列表
    public function actionGetAccreditAll() {

        try {

            $type = $this->type;
            $start_time = trim($this->start_time);
            $end_time = trim($this->end_time);
            $time='';
            
            if (!empty($start_time)) {
                $start_time = strtotime($start_time);
                if (!$start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
            }

            if (!empty($end_time)) {
                $end_time = strtotime($end_time);
                if (!$end_time) {
                    throw new \Exception('结束时间格式有问题', 17055);
                }
            }

            if ($start_time > $end_time && $end_time && $start_time) {
                throw new \Exception('结束时间必须大于开始时间', 17056);
            }
            
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            $i++;
            if (!empty($start_time)) {
                $start_time = date('Y-m-d 00:00:00', $start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($type == 1) {
                    $time = ['>=', 'a.last_login_time', $start_time];
                }
            }
            if (!empty($end_time)) { 
                $end_time = date('Y-m-d 23:59:59', $end_time);
                $time =['<', 'a.created_time', $end_time];
                if ($type == 1) {
                    $time =['<', 'a.last_login_time', $end_time];
                }
            }
          
            if (!empty($end_time) && !empty($start_time)) {
                $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                if ($type == 1) {
                    $time = [ 'and', ['>=', 'a.last_login_time', $start_time], ['<', 'a.last_login_time', $end_time]];
                }
            } 
            
            if($time){
                $condition[$i] = $time;
            }

//            if ($this->property_id != NULL) {
//                $i++;
//                $condition[$i] = 'b.property_id=' . $this->property_id;
//            }


            if ($this->account != NULL) {
                $i++;
                $account_like = ['like', 'a.account', $this->account];
                $condition[$i] = $account_like;
            }

            $i++;
            $condition[$i] = 'a.status=1';

            $i++;
            $condition[$i] = 'a.created_by=-1';
           
            $result = Select::getAccredit($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            if ($ex->getCode() === 17054) {
                $this->addError('getaccreditall', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getaccreditall', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getaccreditall', 17056);
                return false;
            } else {
                $this->addError('getaccreditall', 2005);
                return false;
            }
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'getaccreditall' => $result->models,
        ];
    }

    //获取禁用门店列表
    public function actionGetForbiddenAll() {
        try {

            $type = $this->type;
            $start_time = trim($this->start_time);
            $end_time = trim($this->end_time);
            $time='';
            
            if (!empty($start_time)) {
                $start_time = strtotime($start_time);
                if (!$start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
            }

            if (!empty($end_time)) {
                $end_time = strtotime($end_time);
                if (!$end_time) {
                    throw new \Exception('结束时间格式有问题', 17055);
                }
            }

            if ($start_time > $end_time && $end_time && $start_time) {
                throw new \Exception('结束时间必须大于开始时间', 17056);
            }
            
            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            $i++;
            if (!empty($start_time)) {
                $start_time = date('Y-m-d 00:00:00', $start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($type ==2) {
                    $time = ['>=', 'a.last_modified_time', $start_time];
                }
            }
            if (!empty($end_time)) { 
                $end_time = date('Y-m-d 23:59:59', $end_time);
                $time =['<', 'a.created_time', $end_time];
                if ($type == 2) {
                    $time =['<', 'a.last_modified_time', $end_time];
                }
            }
          
            if (!empty($end_time) && !empty($start_time)) {
                $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                if ($type == 2) {
                    $time = [ 'and', ['>=', 'a.last_modified_time', $start_time], ['<', 'a.last_modified_time', $end_time]];
                }
            } 
            
            if($time){
                $condition[$i] = $time;
            }
     
//
//            if ($this->property_id != NULL) {
//                $i++;
//                $condition[$i] = 'b.property_id=' . $this->property_id;
//            }

            if ($this->account != NULL) {
                $i++;
                $account_like = ['like', 'a.account', $this->account];
                $condition[$i] = $account_like;
            }

            $i++;
            $condition[$i] = 'a.status=2';

            $i++;
            $condition[$i] = 'a.created_by=-1';

            $result = Select::getForbidden($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {

            if ($ex->getCode() === 17054) {
                $this->addError('getforbiddenall', 17054);
                return false;
            } elseif ($ex->getCode() === 17055) {
                $this->addError('getforbiddenall', 17055);
                return false;
            } elseif ($ex->getCode() === 17056) {
                $this->addError('getforbiddenall', 17056);
                return false;
            } else {
                $this->addError('getforbiddenall', 2005);
                return false;
            }
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'getforbiddenall' => $result->models,
        ];
    }

}
