<?php

/**
 * CRM system for 9daye
 * 
 * @author Vett <niulechuan@9daye.com.cn>
 */
namespace commodity\modules\commodityManage\models\delete;

use common\models\Model as BaseModel;
use commodity\modules\commodityManage\models\delete\db\DeleteCommodity;

class DeleteModel extends BaseModel
{
    const ACTION_ONE = 'action_one';
    const ACTION_BATCH = 'action_batch';
    
    public $commodity_id;public $commoditys = [];
    
    public function scenarios()
    {
        return [
            self::ACTION_ONE    => ['commodity_id'],
            self::ACTION_BATCH  => ['commoditys'],
        ];
    }
    
    public function rules()
    {
        return [
            [
                ['commodity_id', 'commoditys'],
                'required',
                'message' => 1,
            ],
        ];
    }
    
    public function actionOne()
    {
        try {
            $count = DeleteCommodity::deleteOne($this->commodity_id);
        } catch(\Exception $ex) {
            if($ex->getMessage() == DeleteCommodity::COMMODITY_NOT_FOUND) {
                $this->addError('actionOne', 2011);
                return false;
            }
        }
        return [
            'commodity_id'    => $this->commodity_id
        ];
    }
    
    public function actionBatch()
    {
        try {
            $results = DeleteCommodity::deleteBatch($this->commoditys);
        } catch(\Exception $ex) {
            if($ex->getMessage() == DeleteCommodity::COMMODITYS_FORMAT_ERROR) {
                $this->addError('actionOne', 2011);
                return false;
            } elseif($ex->getMessage() == DeleteCommodity::COMMODITY_NOT_CLEAN) {
                $this->addError('actionOne', 2015);
                return false;
            }
        }
        return true;
    }
    
    private function countSuccess($array)
    {
        $count = 0;
        foreach($array as $arr) {
            if($arr[1] === true)
                $count++;
        }
        return $count;
    }
}
