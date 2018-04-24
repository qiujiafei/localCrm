<?php

/**
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace commodity\modules\carbasicinformation\models\get;

use common\models\Model as CommonModel;
use commodity\modules\carbasicinformation\models\get\db\Select;
use moonland\phpexcel\Excel;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETCARNUMBERALPHABET = 'action_getCarNumberAlphabet';
    const ACTION_GETCARNUMBERPROVINCE = 'action_getCarNumberProvince';
    const ACTION_GETCARALPHABETHOME = 'action_getCarAlphabetHome';
    const ACTION_GETCARBRANDHOME = 'action_getCarBrandHome';
    const ACTION_GETCARTYPEHOME = 'action_getCarTypeHome';
    const ACTION_GETCARSTYLEHOME = 'action_getCarStyleHome';

    public $count_per_page;
    public $page_num;
    public $id;
    public $keyword;
    public $store_id;
    public $status;
    public $alphabet_id;
    public $brand_id;
    public $type_id;

    public function scenarios() {
        return [
            self::ACTION_GETCARNUMBERALPHABET => [],
            self::ACTION_GETCARNUMBERPROVINCE => [],
            self::ACTION_GETCARALPHABETHOME => [],
            self::ACTION_GETCARBRANDHOME => ['alphabet_id'],
            self::ACTION_GETCARTYPEHOME => ['alphabet_id', 'brand_id'],
            self::ACTION_GETCARSTYLEHOME => ['alphabet_id', 'brand_id', 'type_id'],
        ];
    }

    public function rules() {
        return [
            [
                ['id', 'alphabet_id', 'brand_id', 'type_id', 'token'],
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
            ['status', 'default', 'value' => 0],
        ];
    }

    //车牌字母表
    public function actionGetCarNumberAlphabet() {
        try {
            $field = 'id,name';
            return Select::getCarNumberAlphabet(array(), $field);
        } catch (\Exception $ex) {
            $this->addError('getCarNumberAlphabet', 2005);
            return false;
        }
    }

    //车牌省份表
    public function actionGetCarNumberProvince() {
        try {
            $field = 'id,name';
            return Select::getCarNumberProvince(array(), $field);
        } catch (\Exception $ex) {
            $this->addError('getCarNumberProvince', 2005);
            return false;
        }
    }

    //汽车信息字母表
    public function actionGetCarAlphabetHome() {
        try {
            $field = 'id,name';
            return Select::getCarAlphabetHome(array(), $field);
        } catch (\Exception $ex) {
            $this->addError('getCarAlphabetHome', 2005);
            return false;
        }
    }

    //汽车信息品牌表
    public function actionGetCarBrandHome() {
        try {
            $condition['a.alphabet_id'] = $this->alphabet_id;
            $result['car_brand_home'] = Select::getCarBrandHome($condition);
            return $result;
        } catch (\Exception $ex) {
            $this->addError('getCarBrandHome', 2005);
            return false;
        }
    }

    //汽车型号表
    public function actionGetCarTypeHome() {
        try {
            $condition['a.alphabet_id'] = $this->alphabet_id;
            $condition['a.brand_id'] = $this->brand_id;
            $result['car_type_home'] = Select::getCarTypeHome($condition);
            return $result;
        } catch (\Exception $ex) {
            $this->addError('getCarBrandHome', 2005);
            return false;
        }
    }

    //汽车车款表
    public function actionGetCarStyleHome() {
        try {
            $condition['a.alphabet_id'] = $this->alphabet_id;
            $condition['a.brand_id'] = $this->brand_id;
            $condition['a.type_id'] = $this->type_id;
            $result['car_style_home'] = Select::getCarStyleHome($condition);
            return $result;
        } catch (\Exception $ex) {
            $this->addError('getCarStyleHome', 2005);
            return false;
        }
    }

}
