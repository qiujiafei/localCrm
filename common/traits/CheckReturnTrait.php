<?php
/**
 * Created by PhpStorm.
 * User: lishuang
 * Date: 2017/3/9
 * Time: 下午2:13
 */

namespace common\traits;


use common\ActiveRecord\OSSUploadFileAR;
use common\models\parts\OSSImage;

trait CheckReturnTrait
{

    //验证oss图片信息
    public static function checkImage($fileName)
    {
        $images = new OSSImage([
            'images' => ['filename' => $fileName],
        ]);
        $imagesOwnerType = array_unique($images->getUploaderType());
        $imagesOwnerId = array_unique($images->getUploaderId());
        if (count($imagesOwnerType) > 1 || count($imagesOwnerId) > 1 || current($imagesOwnerType) != OSSUploadFileAR::ADMIN_USER || current($imagesOwnerId) != \Yii::$app->user->id)
        {
            return false;
        }
        return $images;
    }

    //获取树模型
    public static function returnTree($categories)
    {
        $tree = [];
        //第一步，将分类id作为数组key,并创建children单元
        foreach ($categories as $category)
        {
            $tree[$category['id']] = $category;
            $tree[$category['id']]['children'] = [];
        }
        foreach ($tree as $key => $item)
        {
            if ($item['pid'] != 0)
            {
                $tree[$item['pid']]['children'][] = &$tree[$key];//注意：此处必须传引用否则结果不对
                if ($tree[$key]['children'] == null)
                {
                    unset($tree[$key]['children']); //如果children为空，则删除该children元素（可选）
                }
            }
        }
        foreach ($tree as $key => $category)
        {
            if ($category['pid'] != 0)
            {
                unset($tree[$key]);
            }
        }

        return $tree;

    }


    //下标数组转换为Map（即关联数组）
    public static function index2map($indexArray, $keyName, $valueName = false)
    {
        if (empty($indexArray))
        {
            return [];
        }
        $map = [];
        foreach ($indexArray as $ele)
        {
            $key = $ele[$keyName];
            $map[$key] = $valueName != false ? $ele[$valueName] : $ele;
        }
        return $map;
    }


}