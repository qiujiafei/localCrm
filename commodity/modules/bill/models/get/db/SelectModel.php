<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/1/5
 * Time: 13:34
 */

namespace commodity\modules\bill\models\get\db;

use common\ActiveRecord\BillAR;
use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CustomerCarsAR;
use common\ActiveRecord\CustomerInfomationAR;
use common\ActiveRecord\ServiceAR;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use Yii;

class SelectModel extends BillAR {

    const DEFAULT_COUNT_PER_PAGE = 10;
    const DEFAULT_PAGE_NUM = 1;

    public static function findLists($where, $pageSize = 20) {
        $query = self::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);

        $list = $query
                ->where($where)
                ->with('customerInformation')
                ->with('customerCars')
                ->with('service')
                //->with('commodity')
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->orderBy('created_time DESC')
                ->all();

        return [
            'lists' => $list,
            'count' => $pagination->pageSize,
            'total_count' => $pagination->totalCount
        ];
    }

    /**
     * 获取客户信息
     */
    public function getCustomerInformation() {
        return $this->hasOne(CustomerInfomationAR::className(), ['id' => 'customer_infomation_id']);
    }

    /**
     * 获取客户汽车
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerCars() {
        return $this->hasOne(CustomerCarsAR::className(), ['customer_infomation_id' => 'customer_infomation_id']);
    }

    public function getService() {
        return $this->hasMany(ServiceAR::className(), ['id' => 'service_id'])->viaTable('{{%bill_service}}', ['bill_id' => 'id']);
    }

    /**
     * 通过开单ID获取商品名称
     * @param int $billId
     * @return int
     * @throws \yii\db\Exception
     */
    public function getCommodityNames($billId = 0) {
        $sql = "
        SELECT c.commodity_name from {{%commodity}} c,{{%picking_commodity}} pc,{{%picking}} p,{{%bill_picking}} bp,{{%bill}} b where b.id=:billId and
            bp.bill_id = b.id and p.id = bp.picking_id and pc.commodity_id = c.id;
        ";
        return Yii::$app->db->createCommand($sql, ['billId' => $billId])->queryAll();
    }

    /**
     * 获取服务和商品
     * @param $service
     * @param string $commodity
     * @return array
     */
    public function getMergeServiceAndCommodity($service, $commodity) {
        $names = [];
        if ($service) {
            foreach ($service as $data) {
                $names['service'][] = $data['service_name'];
            }
        }
        if (count($commodity)) {
            $names['commodity'] = array_column($commodity, 'commodity_name');
        }

        return $names;
    }

    public static function getone($condition) {

        self::verifyStoreId();

        return self::find()
                        ->select([
                            'a.id',
                            'a.bill_number',
                            'a.customer_infomation_id', //must modifed
                            'b.customer_name',
                            'b.customer_origination',
                            'h.card_number',
                            'k.frame_number',
                            'c.name as number_plate_province_name',
                            'd.name as number_plate_alphabet_name',
                            'k.number_plate_number',
                            'f.name as alphabet_name',
                            'g.name as brand_name',
                            'e.name as style_name',
                            'e.year',
                            'b.cellphone_number',
                            'a.created_time',
                            'a.price',
                            'j.name as created_name',
                            'a.comment',
                        ])
                        ->from('crm_bill as a')
                        ->join('LEFT JOIN', 'crm_customer_infomation As b', 'a.customer_infomation_id = b.id')
                        ->join('LEFT JOIN', 'crm_customer_cars As k', 'b.id = k.customer_infomation_id')
                        ->join('LEFT JOIN', 'crm_customer_infomation As s', 's.id = k.customer_infomation_id')
                        ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = k.number_plate_province_id')
                        ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = k.number_plate_alphabet_id')
                        ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = k.model_id')
                        ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                        ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                        ->join('LEFT JOIN', 'crm_member As h', 'a.customer_infomation_id = h.customer_infomation_id')
                        ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = a.created_by')
                        ->where($condition)
                        ->asArray()
                        ->one();
    }

    public static function getnoall($count_per_page, $page_num, $condition) {

        self::verifyStoreId();

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()
                    ->select([
                        'a.id',
                        'a.bill_number',
                        'a.customer_infomation_id', //must modifed
                        'b.customer_name',
                        'c.name as number_plate_province_name',
                        'd.name as number_plate_alphabet_name',
                        'k.number_plate_number',
                        'k.frame_number',
                        'f.name as alphabet_name',
                        'g.name as brand_name',
                        'r.name as type_name',
                        'e.name as style_name',
                        'e.year',
                        'b.cellphone_number',
                        'a.created_time',
                        'a.price',
                        'a.final_price',
                        'j.name as technician_name',
                        'a.comment',
                    ])
                    ->from('crm_bill as a')
                    ->join('LEFT JOIN', 'crm_customer_infomation As b', 'a.customer_infomation_id = b.id')
                    ->join('LEFT JOIN', 'crm_customer_cars As k', 'b.id = k.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = k.number_plate_province_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = k.number_plate_alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = k.model_id')
                    ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                     ->join('LEFT JOIN', 'crm_car_type_home As r', 'r.id = e.type_id')
                    ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                    ->join('LEFT JOIN', 'crm_member As h', 'a.id = h.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_employee As j', 'j.id = a.technician_id')
                    ->where($condition)
                    ->asArray(),
            'pagination' => [
                'page' => $page_num - 1,
                'pageSize' => $count_per_page,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_time' => SORT_DESC,
                ],
            ],
        ]);
    }

    public static function getall($count_per_page, $page_num, $condition) {

        self::verifyStoreId();

        if (!isset($page_num) || $page_num < 1) {
            $page_num = self::DEFAULT_PAGE_NUM;
        }

        if (!isset($count_per_page) || $count_per_page < 1) {
            $count_per_page = self::DEFAULT_COUNT_PER_PAGE;
        }

        return new ActiveDataProvider([
            'query' => self::find()
                    ->select([
                        'a.id',
                        'a.bill_number',
                        'a.customer_infomation_id', //must modifed
                        'b.customer_name',
                        'c.name as number_plate_province_name',
                        'd.name as number_plate_alphabet_name',
                        'k.number_plate_number',
                        'k.frame_number',
                        'f.name as alphabet_name',
                        'g.name as brand_name',
                        'r.name as type_name',
                        'e.name as style_name',
                        'e.year',
                        'b.cellphone_number',
                        'a.is_member',
                        'a.member_discount',
                        'a.price',
                        'a.final_price',
                        'a.created_time',
                        'a.last_modified_time',
                        'm.name as technician_name',
                        'j.name as created_name',
                        'a.comment',
                    ])
                    ->from('crm_bill as a')
                    ->join('LEFT JOIN', 'crm_customer_infomation As b', 'a.customer_infomation_id = b.id')
                    ->join('LEFT JOIN', 'crm_customer_cars As k', 'b.id = k.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = k.number_plate_province_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = k.number_plate_alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = k.model_id')
                    ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                    ->join('LEFT JOIN', 'crm_car_type_home As r', 'r.id = e.type_id')
                    ->join('LEFT JOIN', 'crm_member As h', 'a.id = h.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = a.created_by')
                    ->join('LEFT JOIN', 'crm_employee As m', 'm.id = a.technician_id')
                    ->where($condition)
                    ->asArray(),
            'pagination' => [
                'page' => $page_num - 1,
                'pageSize' => $count_per_page,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_time' => SORT_DESC,
                ],
            ],
        ]);
    }

    public static function getexport($condition) {

        return  self::find()
                    ->select([
                        'a.id',
                        'a.bill_number',
                        'a.customer_infomation_id', //must modifed
                        'b.customer_name',
                        'c.name as number_plate_province_name',
                        'd.name as number_plate_alphabet_name',
                        'k.number_plate_number',
                        'k.frame_number',
                        'f.name as alphabet_name',
                        'g.name as brand_name',
                        'e.name as style_name',
                        'e.year',
                        'b.cellphone_number',
                        'b.is_member',
                        'a.member_discount',
                        'a.created_time',
                        'a.price',
                        'a.final_price',
                        'm.name as technician_name',
                        'j.name as created_name',
                        'a.comment',
                        'n.name as last_modified_name',
                    ])
                    ->from('crm_bill as a')
                    ->join('LEFT JOIN', 'crm_customer_infomation As b', 'a.customer_infomation_id = b.id')
                    ->join('LEFT JOIN', 'crm_customer_cars As k', 'b.id = k.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_province As c', 'c.id = k.number_plate_province_id')
                    ->join('LEFT JOIN', 'crm_customer_cars_number_plate_alphabet As d', 'd.id = k.number_plate_alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_style_home As e', 'e.id = k.model_id')
                    ->join('LEFT JOIN', 'crm_car_alphabet_home As f', 'f.id = e.alphabet_id')
                    ->join('LEFT JOIN', 'crm_car_brand_home As g', 'g.id = e.brand_id')
                    ->join('LEFT JOIN', 'crm_member As h', 'a.id = h.customer_infomation_id')
                    ->join('LEFT JOIN', 'crm_employee_user As j', 'j.id = a.created_by')
                    ->join('LEFT JOIN', 'crm_employee As m', 'm.id = a.technician_id')
                    ->join('LEFT JOIN', 'crm_employee_user As n', 'n.id = a.last_modified_by')
                    ->where($condition)
                    ->asArray()
                    ->all();
    }
    
    public static function getcount(array $condition = array()) {
        return BillAR::find()->from('crm_bill as a')
                                    ->join('LEFT JOIN', 'crm_customer_infomation As b', 'a.customer_infomation_id = b.id')
                                    ->where($condition)
                                    ->count();
    }

    public static function getUser() {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }

    public static function verifyStoreId() {

        $user = current(self::getUser());

        if (!isset($user->store_id)) {
            throw new \Exception("Unknown error. Sames can not get user's store.");
        }
    }

    /**
     * 获取当前门店所有的营业额
     * @param $storeId
     * @return mixed
     */
    public function getFinalPriceAllOfStore($storeId)
    {
        $where = ['store_id' => $storeId];
        return self::find()->where($where)->sum('final_price') ?? 0;
    }

    /**
     * 今日营业金额
     * @param $storeId
     * @param $startTime
     * @param $endTime
     * @return mixed
     */
    public function getTurnoverByToday($storeId,$startTime,$endTime)
    {
        $where = [
            'and',
            ['store_id' => $storeId],
            ['between','created_time',$startTime,$endTime]
        ];

        return self::find()->where($where)->sum('final_price');
    }

}
