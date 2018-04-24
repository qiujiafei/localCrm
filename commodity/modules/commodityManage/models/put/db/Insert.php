<?php

/*  * 
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */

namespace commodity\modules\commodityManage\models\put\db;

use common\ActiveRecord\CommodityAR;
use common\ActiveRecord\CommodityImageAR;
use commodity\components\tokenAuthentication\AccessTokenAuthentication;
use commodity\modules\commodityManage\models\get\db\GetClassificationIdByName;
use commodity\modules\commodityManage\models\get\db\GetUnitIdByName;
use commodity\modules\commodityManage\models\judgment\HasSameCommodity as Judgment;
use Yii;

class Insert extends CommodityAR
{
    const IMAGE_COUNT_LIMIT = 5;
    
    public static $commodityKeys = [
        'commodity_name' => '',
        'specification' => '',
        'commodity_code' => '',
        'classification_id' => '',
        'classification_name' => '',
        'price' => '',
        'barcode' => '',
        'unit_name' => '',
        'commodity_property_name' => 1,
        'status' => 1,
        'comment' => '',
        'store_id' => '',
        'commodity_originate_id' => 1,
        'created_by' => '',
        'created_time' => '',
        'barcode' => '',
        'default_depot_id' => '',
        'commodity_name' => '',
        'last_modified_by' => '',
        'last_modified_time' => '',
        'images' => [],
    ];
    
    public static $imageKeys = [
        'commodity_name' => '',
        'barcode' => '',
        'store_id' => '',
        'image_url' => '',
        'image_name' => '',
        'last_modified_by' => '',
        'last_modified_time' => '',
        'created_by' => '',
        'created_time' => '',
        'status' => 1,
    ];
    
    public static $propertyKeys = [
        'property_name' => '',
        'store_id' => '',
        'status' => 1,
        'created_by' => '',
        'created_time' => '',
    ];
    
    /**
     * Insert data into DB.
     * 
     * format:
     * 
     * $data  = [
     *   'barcode' => '',
     *   'commodity_id' => '',
     *   'commodity_name' => '',
     *   'specification' => '',
     *   'price' => '',
     *   'classification_name' => '',
     *   'unit_name' => '',
     *   'comment' => '',
     *   'images' => [
     *      ['image_name' => ''],
     *      ['image_name' => ''],
     *   ],
     * ];
     * 
     */
    public static function insertCommodity(array $data)
    {
        if((new Judgment($data))()) {
            throw new \Exception("", 23000);
        }
        try {
            $data = self::prepareData($data);
        } catch(\Exception $ex) {
            throw $ex;
        }

        $images = $data['images'];
        
        if(count($images) > self::IMAGE_COUNT_LIMIT) {
            throw new \Exception(sprintf(
                'Images should less than %s. In %s.', self::IMAGE_COUNT_LIMIT, __METHOD__
            ), 2003);
        }
        
        unset($data['images']);
        
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $commodityInsert = new self;

            foreach($data as $k => $v) {
                $commodityInsert->$k = $v;
            }
            
            $commodityInsert->save(false);
        
            self::insertCommodityImages($images);
            
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
        
        return self::$commodityKeys;
    }
   
    public static function insertCommodityImages(array $images)
    {
        $commodityImage = new CommodityImageAR();

        $sql = $commodityImage
                ->getDb()
                ->queryBuilder
                ->batchInsert(
                        $commodityImage->getTableSchema()->fullName,
                        array_keys(self::$imageKeys),
                        $images
                );

        return $commodityImage->getDb()->createCommand($sql)->execute();
    }
    
    public static function prepareData(array $data)
    {
        //判断user是否存在
        if(!$userIdentity = AccessTokenAuthentication::getUser()) {
            throw new \Exception(sprintf(
                "Can not found user identity in %s.", __METHOD__
            ));
        }
        
        //过滤commodity无效参数
        foreach(self::$commodityKeys as $key => $value) {
            self::$commodityKeys[$key] = $data[$key] ?? $value;
        }
        
        //生成image参数
        if(!empty(self::$commodityKeys['images'])) {
            foreach(self::$commodityKeys['images'] as $key => $image) {
                unset(self::$commodityKeys['images'][$key]);
                self::$commodityKeys['images'][$key]['commodity_name']      = self::$commodityKeys['commodity_name'] ?? '';
                self::$commodityKeys['images'][$key]['barcode']             = self::$commodityKeys['barcode'] ?? '';
                self::$commodityKeys['images'][$key]['store_id']            = $userIdentity['store_id'];
                self::$commodityKeys['images'][$key]['image_url']           = Yii::$app->params['OSS_PostHost'];
                self::$commodityKeys['images'][$key]['image_name']          = $image;
                self::$commodityKeys['images'][$key]['last_modified_by']    = $userIdentity['id'];
                self::$commodityKeys['images'][$key]['last_modified_time']  = date('Y-m-d H:i:s');
                self::$commodityKeys['images'][$key]['created_by']          = $userIdentity['id'];
                self::$commodityKeys['images'][$key]['created_time']        = date('Y-m-d H:i:s');
                self::$commodityKeys['images'][$key]['status']              = 1;
            }
        }
        
        //添加固定参数
        try {
            self::$commodityKeys['classification_id']   = (new GetClassificationIdByName)(self::$commodityKeys['classification_name']);
            self::$commodityKeys['unit_id']   = (new GetUnitIdByName)(self::$commodityKeys['unit_name']);
        } catch(\Exception $ex) {
            throw $ex;
        }
        self::$commodityKeys['created_by']          = $userIdentity['id'];
        self::$commodityKeys['store_id']            = $userIdentity['store_id'];
        self::$commodityKeys['last_modified_by']    = $userIdentity['id'];
        self::$commodityKeys['last_modified_time']  = date('Y-m-d H:i:s');
        self::$commodityKeys['created_time']        = date('Y-m-d H:i:s');
        self::$commodityKeys['price']               = round(self::$commodityKeys['price'], 2);

        return self::$commodityKeys;
    }
    
    public static function getUser()
    {
        return Yii::$app->user->getIdentity()::$user ?? null;
    }
}
