<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2018/2/7
 * Time: 10:23
 * @author hejinsong@9daye.com.cn
 * 服务项目查询
 */

namespace commodity\modules\bill\logics;
use common\ActiveRecord\ServiceAR;
use yii\data\Pagination;

class ServiceLogicModel extends ServiceAR
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
                $where[] = ['like','service_code',$value];
                continue;
            }
            if ($value) {
                $where[] = ['like','service_name',$value];
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

}