<?php

/* *
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\testAccount\models\get;

use common\models\Model as CommonModel;
use admin\modules\testAccount\models\get\db\Select;
use admin\components\tokenAuthentication\AccessTokenAuthentication;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETONE = 'action_getone';
    const ACTION_GETACCREDITALL = 'action_getaccreditall';
    const ACTION_GETFORBIDDENALL = 'action_getforbiddenall';

    public $count_per_page;
    public $page_num;
    public $id;
    public $property; //门店性质
    public $last_login_time; //最后登陆时间
    public $start_time; //开始时间
    public $end_time; //结束时间

    public function scenarios() {
        return [
            self::ACTION_GETONE => ['id', 'account_name'],
            self::ACTION_GETACCREDITALL => ['count_per_page', 'page_num', 'property', 'last_login_time', 'start_time', 'end_time'],
            self::ACTION_GETFORBIDDENALL => ['count_per_page', 'page_num', 'property', 'last_login_time', 'start_time', 'end_time'],
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

    public function actionGetone() {
        try {
            $result = Select::getone($this->id);
            if (!$result) {
                throw new \Exception('无法获取-1', 2005);
                return false;
            }
            return $result;
        } catch (\Exception $ex) {
            $this->addError('getone', 2005);
            return false;
        }

        return $result;
    }
    

    //获取授权列表
    public function actionGetAccreditAll() {
        try {
//            $condition = [];
//            if (!empty($this->property)) {
//                $condition = ['like', 'account_name', $this->property];
//            }
//            $start_time = $this->start_time;
//            $end_time = $this->end_time;
//            if ($start_time) {
//                $v_start_time = strtotime($start_time);
//                if (!$v_start_time) {
//                    throw new \Exception('开始时间格式有问题', 17054);
//                }
//                if ($end_time) {
//                    $v_end_time = strtotime($end_time);
//                    if (!$v_end_time) {
//                        throw new \Exception('结束时间格式有问题', 17055);
//                    }
//                    if ($v_start_time >= $v_end_time) {
//                        throw new \Exception('结束时间必须大于开始时间', 17056);
//                    }
//                }
//            }
//
//            $condition = array();
//            $i = 0;
//            $condition[0] = 'and';
//            if ($start_time) {
//                $start_time = date('Y-m-d H:i:s', $v_start_time);
//                $time = ['>=', 'h.created_time', $start_time];
//                if ($end_time) {
//                    $end_time = date('Y-m-d H:i:s', $v_end_time);
//                    $time = [ 'and', ['>=', 'h.created_time', $start_time], ['<', 'h.created_time', $end_time]];
//                }
//                $i++;
//                $condition[$i] = $time;
//            }
//
//            $i++;
//            $condition[$i] = 'a.status=' . $status;

            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
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
            'testAccount' => $result->models,
        ];
    }
    

    //获取禁用门店列表
    public function actionGetForbiddenAll() {
        try {
            $condition = [];
            if (!empty($this->keyword)) {
                $condition = ['like', 'account_name', $this->keyword];
            }
            $result = Select::getall($this->count_per_page, $this->page_num, $condition);
        } catch (\Exception $ex) {
            $this->addError('getall', 2005);
            return false;
        }

        return [
            'count' => $result->count,
            'total_count' => $result->totalCount,
            'testAccount' => $result->models,
        ];
    }

}
