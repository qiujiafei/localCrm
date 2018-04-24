<?php
/**
 * CRM system for 9daye
 *
 * @author: wj <wangjie@9daye.com.cn>
 */

namespace commodity\modules\memberPoint\models\memberPoint;

use commodity\modules\memberPoint\models\memberPoint\put\Insert;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;
use commodity\modules\memberPoint\models\memberPoint\get\Select;
use Yii;

class MemberPointModel extends CommonModel
{
    const ACTION_COUNT_BLOCK = 'action_count_block';
    const ACTION_PUT_RATE = 'action_put_rate';
    const ACTION_GET_MEMBER_INFO = 'action_get_member_info';

    public $token;
    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $store_id;
    public $status;
    public $recharge_rate;    //充值积分比例值
    public $consumption_rate;    //消费积分比例值

    public function scenarios()
    {
        return [
            self::ACTION_COUNT_BLOCK => [],
            self::ACTION_PUT_RATE => ['recharge_rate','consumption_rate'],
            self::ACTION_GET_MEMBER_INFO => ['store_id'],
        ];
    }

    public function rules()
    {
        return [
            [
                ['store_id', 'token'],
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
            ['status', 'default', 'value' => 1],
        ];
    }


    //积分统计接口
    public function actionCountBlock()
    {
        try {
            $result = [];
            $result['day'] = $this->getDay();
            $result['moon'] = $this->getMoon();
            $result['total'] = $this->getTotal();
        } catch (\Exception $ex) {
            $this->addError('count-block', 2005);
        }

        if ($result == false)
        {
            return true;
        }

        return $result;
    }

    //设置兑换积分比例值接口
    public function actionPutRate()
    {
        try {
            $data['recharge_rate'] = $this->recharge_rate;
            $data['consumption_rate'] = $this->consumption_rate;
            $data['store_id'] = $this->getStoreId();
            $data['created_by'] = $this->getCreatedBy();
            $data['last_modified_time'] = date('Y-m-d H:i:s');
            $data['created_time'] = date('Y-m-d H:i:s');
            $result = Insert::insertRate($data);
        } catch (\Exception $ex) {
            $this->addError('insert_recharge', 2005);
            return false;
        }
        if($result == false)
        {
            $this->addError('添加失败。。。', 2005);
        }

        return $data;
    }

    //获取客户积分信息接口
    public function actionGetMemberInfo()
    {
        try {
            $result = Select::getMemberInfo($this->getStoreId());
        } catch (\Exception $ex) {
            $this->addError('get-member-info', 2005);
            return false;
        }

        if ($result == false) {
            return true;
        }

        return $result;
    }

    //获取当前账号的门店id
    protected function getStoreId()
    {
        return AccessTokenAuthentication::getUser()['store_id']??0;
    }

    //获取当前账号门店员工id
    protected function getCreatedBy()
    {
        return AccessTokenAuthentication::getUser()['id']??0;
    }

    //获取用户总积分
    protected function getTotal()
    {
        try {
            $result = Select::getTotal($this->getStoreId());
        } catch (\Exception $ex) {
            $this->addError('get-total', 2005);
            return false;
        }

        //如果没有数据，返回空数据
        if ($result == false){
            return true;
        }

        $total = [];
        foreach ($result as $value){
            foreach ($value as $v){
                $total[] = $v;
            }
        }
        $data = array_sum($total);
        unset($total);
        unset($result);

        return $data;
    }

    //获取当日新增积分
    protected function getDay()
    {
        try {
            $today_start = date('Y-m-d 00:00:00');
            $today_end = date('Y-m-d 23:59:59');
            $result = Select::getDayPoint($this->getStoreId(), $today_start,$today_end);
        } catch (\Exception $e) {
            $this->addError('get-day', 2005);
            return false;
        }

        if ($result == false)
        {
            return true;
        }

        $total = [];
        foreach ($result as $value){
            foreach ($value as $v){
                $total[] = $v;
            }
        }

        $data = array_sum($total);
        unset($total);
        unset($result);

        return $data;
    }

    //获取当月新增积分
    protected function getMoon()
    {
        try {
            $begin = date('Y-m-01', strtotime(date("Y-m-d H:i:s")));
            $end = date('Y-m-d', strtotime("$begin +1 month -1 day"));
            $result = Select::getMoonPoint($this->getStoreId(), $begin, $end);
        } catch (\Exception $e) {
            $this->addError('get-moon', 2005);
            return false;
        }

        if ($result == false)
        {
            return true;
        }

        $total = [];
        foreach ($result as $value){
            foreach ($value as $v){
                $total[] = $v;
            }
        }

        $data = array_sum($total);
        unset($total);
        unset($result);

        return $data;
    }

}