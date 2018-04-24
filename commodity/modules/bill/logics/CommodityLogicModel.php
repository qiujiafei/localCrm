<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/7
 * Time: 10:23
 * @author hejinsong@9daye.com.cn
 * 开单库存相关商品
 */

namespace commodity\modules\bill\logics;
use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityBatchAR;
use common\ActiveRecord\DepotAR;
use yii\data\Pagination;

class CommodityLogicModel extends CommodityAR
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
            if ($value) {
                $where[] = ['like','commodity_name',$value];
            }
        }

        if ( ! empty($keyword)) {
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
        $andWhere = $where['and'];
        unset($where['and']);

        $query = self::find()->alias('c')->where($where)->andWhere($andWhere)
            ->leftJoin('{{%commodity_batch}} cb','cb.commodity_id = c.id and cb.stock != 0');
        $count = $query->groupBy('commodity_id,depot_id')->count();
        $pagination = new Pagination(['totalCount' => $count,'pageSize'=>$pageSize]);

        $list = $query
            ->with('batch')
            ->with('depot')
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

    public function getBatch()
    {
        return $this->hasMany(CommodityBatchAR::className(),['commodity_id'=>'id']);
    }

    /**
     * 获取仓库数据，这里只考虑只有一个仓库的问题
     */
    public function getDepot()
    {
        return $this->hasOne(DepotAR::className(),['id'=>'depot_id'])->viaTable('{{%commodity_batch}}',['commodity_id'=>'id']);
    }
}