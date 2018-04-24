<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/9
 * Time: 13:20
 */

namespace commodity\modules\store\models\get;

use commodity\modules\store\models\StoreLogicModel;
use commodity\modules\store\models\StoreObject;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\models\UserInfoLogic;
use commodity\modules\store\models\get\db\SelectModel;

/**
 * Class GetModel
 * @package commodity\modules\depot\models\get
 */
class GetModel extends CommonModel {

    const ACTION_LISTS = 'action_lists';
    const ACTION_ONE = 'action_one';
    const ACTION_GETACCREDITALL = 'action_getaccreditall';
    const ACTION_GETFORBIDDENALL = 'action_getforbiddenall';

    public $token;
    public $store_id;
    public $depot_name;
    public $page;
    public $pageSize;
    public $id;
    public $property_id; //门店性质
    public $last_login_time; //最后登陆时间
    public $start_time; //开始时间
    public $end_time; //结束时间
    public $count_per_page;
    public $page_num;

    public function scenarios() {
        return [
            self::ACTION_LISTS => [
                'token', 'page', 'pageSize'
            ],
            self::ACTION_ONE => [
                'token', 'store_id'
            ],
            self::ACTION_GETACCREDITALL => ['count_per_page', 'page_num', 'property_id', 'last_login_time', 'start_time', 'end_time'],
            self::ACTION_GETFORBIDDENALL => ['count_per_page', 'page_num', 'property_id', 'last_login_time', 'start_time', 'end_time'],
        ];
    }

    /**
     * @return array
     * @throws \Exception
     * 门店列表，若是总店返回其下所有子门店；若是分店，返回自己
     */
    public function actionLists() {
        $model = new SelectModel();
        //门店ID
        $storeId = AccessTokenAuthentication::getUser(true);
        //目前该查询不支持总店的情况，只支持当前门店。
        $storeObject = new StoreObject();
        $where = $storeObject->createListWhereByStoreId($storeId);
        return $model->findList($where, $this->pageSize);
    }

    /**
     * 获取信息，无果返回空数组
     * @return array|bool
     */
    public function actionOne() {
        try {
            //获取当前为当前门店ID
            $id = $storeId = AccessTokenAuthentication::getUser(true);
            $storeObject = new StoreObject();
            //传入店铺ID时
            if ($this->store_id) {
                //当传入的门店ID非当前登录门店ID，那么表示查询其他门店
                //以当前店铺为参考，验证传入店铺id是否是分店
                if ($this->store_id != $storeId && !$storeObject->isOwnerBranchStore($storeId, $this->store_id)) {
                    throw new \Exception('非法操作', 11001);
                }
                $id = $this->store_id;
            }
            $storeObject->getOneById($id);
            return $storeObject->toArray();
        } catch (\Exception $e) {
            $this->addError($e->getMessage(), $e->getCode());
            return false;
        }
    }
    
    
    
    //获取授权列表
    public function actionGetAccreditAll() {
        
        try {
            
            $start_time = $this->start_time;
            $end_time = $this->end_time;
            if ($start_time) {
                $v_start_time = strtotime($start_time);
                if (!$v_start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
                if ($end_time) {
                    $v_end_time = strtotime($end_time);
                    if (!$v_end_time) {
                        throw new \Exception('结束时间格式有问题', 17055);
                    }
                    if ($v_start_time >= $v_end_time) {
                        throw new \Exception('结束时间必须大于开始时间', 17056);
                    }
                }
            }

            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                }
                $i++;
                $condition[$i] = $time;
            }

            if ($this->property_id != NULL) {
                $i++;
                $condition[$i] = 'b.property_id=' . $this->property_id;
            }

            if ($this->last_login_time != NULL) {
                $i++;
                $condition[$i] = 'a.last_login_time=' . $this->last_login_time;
            }

            $i++;
            $condition[$i] = 'b.status=1';

            $i++;
            $condition[$i] = 'a.created_by=-1';

            $result = SelectModel::getAccredit($this->count_per_page, $this->page_num, $condition);
            
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
            'store' => $result->models,
        ];
    }

    

    //获取禁用门店列表
    public function actionGetForbiddenAll() {
        try {
           
            $start_time = $this->start_time;
            $end_time = $this->end_time;
            if ($start_time) {
                $v_start_time = strtotime($start_time);
                if (!$v_start_time) {
                    throw new \Exception('开始时间格式有问题', 17054);
                }
                if ($end_time) {
                    $v_end_time = strtotime($end_time);
                    if (!$v_end_time) {
                        throw new \Exception('结束时间格式有问题', 17055);
                    }
                    if ($v_start_time >= $v_end_time) {
                        throw new \Exception('结束时间必须大于开始时间', 17056);
                    }
                }
            }

            $condition = array();
            $i = 0;
            $condition[0] = 'and';
            if ($start_time) {
                $start_time = date('Y-m-d H:i:s', $v_start_time);
                $time = ['>=', 'a.created_time', $start_time];
                if ($end_time) {
                    $end_time = date('Y-m-d H:i:s', $v_end_time);
                    $time = [ 'and', ['>=', 'a.created_time', $start_time], ['<', 'a.created_time', $end_time]];
                }
                $i++;
                $condition[$i] = $time;
            }

            if ($this->property_id != NULL) {
                $i++;
                $condition[$i] = 'b.property_id=' . $this->property_id;
            }

            if ($this->last_login_time != NULL) {
                $i++;
                $condition[$i] = 'a.last_login_time=' . $this->last_login_time;
            }

            $i++;
            $condition[$i] = 'b.status=2';

            $i++;
            $condition[$i] = 'a.created_by=-1';
            
            $result = SelectModel::getForbidden($this->count_per_page, $this->page_num, $condition);
            
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
            'store' => $result->models,
        ];
    }

}
