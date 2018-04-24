<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/7
 * Time: 10:23
 * @author hejinsong@9daye.com.cn
 * 客户获取接口
 */

namespace commodity\modules\bill\logics;
use common\ActiveRecord\CarBrandHomeAR;
use common\ActiveRecord\CarTypeHomeAR;
use common\ActiveRecord\CustomerCarsAR;
use common\ActiveRecord\CustomerInfomationAR;
use common\ActiveRecord\MemberAR;
use yii\data\Pagination;

class CustomerLogicModel extends CustomerInfomationAR
{
    /**
     * 获取并处理搜索关键词
     * @param string $keyword
     * @return array
     */
    public static function resolveKeyword($keyword = '')
    {
        //按空格进行
        $keywords = explode(' ',$keyword);
        //过滤空值
        $keywords = array_flip(array_flip($keywords));

        $where = [];
        foreach ($keywords as $value){
            if (is_numeric($value)) {
                $where[] = ['like','cellphone_number',$value];
                continue;
            }
            if ($value) {
                $where[] = ['like','customer_name',$value];
            }

        }
        if ( ! empty($where)) {
            array_unshift($where,'or');
        }

        return $where;
    }

    /**
     * 搜索关键字
     * @param $where
     * @param int $pageSize
     * @return array
     */
    public static function findListByKeyword($where,$pageSize=20)
    {
        $query = self::find()->where($where);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $list = $query
            ->where($where)
            ->with('member')
            ->with('cars')
            ->with('carTypeHome')
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

    public function getMember()
    {
        return $this->hasOne(MemberAR::className(),['customer_infomation_id'=>'id']);
    }

    public function getCars()
    {
        return $this->hasOne(CustomerCarsAR::className(),['customer_infomation_id'=>'id']);
    }

    /**
     * 获取车品牌信息
     *
     */
    public function getCarBrand()
    {
        return $this->hasMany(CarBrandHomeAR::className(),['id'=>'id'])
            ->viaTable('{{%car_type_home}}',['id'=>'model_id'])
            ->viaTable('{{%customer_cars}}',['customer_cars_id'=>'id'])
            ->viaTable('{{%customer_infomation_cars}}',['customer_infomation_id'=>'id']);

    }

    /**
     * 获取汽车关联核心信息
     * @return \yii\db\ActiveQuery
     */
    public function getCarTypeHome()
    {
        return $this->hasOne(CarTypeHomeAR::className(),['id'=>'model_id'])->viaTable('{{%customer_cars}}',['customer_infomation_id'=>'id']);
    }
}