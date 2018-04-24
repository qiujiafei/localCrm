<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/7
 * Time: 10:34
 * @author hejinsong@9daye.com.cn
 */

namespace commodity\modules\bill\models\get;
use commodity\modules\bill\logics\CommodityLogicModel;
use commodity\modules\bill\logics\CustomerLogicModel;
use commodity\modules\bill\logics\ServiceLogicModel;
use common\components\tokenAuthentication\AccessTokenAuthentication;
use common\models\Model as CommonModel;

class SearchModel extends CommonModel
{
    const ACTION_CUSTOMERS = 'action_customers';
    const ACTION_SERVICES = 'action_services';
    const ACTION_COMMODITIES = 'action_commodities';

    //搜索关键字
    public $keyword;
    public $page;
    public $pageSize;

    public function scenarios() {
        return [
            self::ACTION_CUSTOMERS => ['keyword','page','pageSize'],
            self::ACTION_SERVICES => ['keyword','page','pageSize'],
            self::ACTION_COMMODITIES => ['keyword','page','pageSize'],
        ];
    }

    public function rules() {
        return [
        ];
    }

    /**
     * 客户信息
     * @return array
     */
    public function actionCustomers()
    {
        //获取搜索关键字，暂不支持卡号搜索，即不支持会员搜索
        $where = CustomerLogicModel::resolveKeyword($this->keyword);
        $lists = CustomerLogicModel::findListByKeyword($where,$this->pageSize);
        $tempData = [];
        foreach($lists['lists'] as $key=>$model){
            $tempData = $model->toArray();
            //会员卡号
            $tempData['card_number'] = null !== $model->member ? $model->member->card_number : '';
            $cars = $model->cars;
            //车架号
            $tempData['frame_number'] = $cars ? $cars->frame_number : '';
            //车牌号
            $tempData['number_plate_number'] = $cars ? $cars->number_plate_number : '';
            //品牌车系
            $carTypeHome = $model->carTypeHome;
            $tempData['brand_of_car'] = $carTypeHome ? $carTypeHome->name : '';
            $lists['lists'][$key] = $tempData;
        }
        return $lists;
    }

    /**
     * 获取服务列表
     * @return array
     */
    public function actionServices()
    {
        $where = ServiceLogicModel::resolveKeyword($this->keyword,$this->pageSize);
        $lists = ServiceLogicModel::findListByKeyword($where);
        return $lists;
    }

    /**
     * 获取商品
     * @return array
     */
    public function actionCommodities()
    {
        $storeId = AccessTokenAuthentication::getUser(true);
        $where = CommodityLogicModel::resolveKeyword($this->keyword,$this->pageSize);
        $where['and'] = [
            'cb.store_id' => $storeId
        ];
        $lists = CommodityLogicModel::findListByKeyword($where);
        $tempData = [];
        foreach ($lists['lists'] as $key=>$model) {
            $tempData = $model->toArray();
            $batches = $model->batch;
            $tempData['stock'] = array_sum(array_column($batches,'stock'));
            $tempData['depot_name'] = $model->depot->depot_name;
            $lists['lists'][$key] = $tempData;
        }
        return $lists;
    }
}