<?php

/* *
 * CRM system for 9daye
 * 
 * @author wx <wangxiong@9daye.com.cn>
 */

namespace admin\modules\sprider\models\get;

ini_set('memory_limit', '3072M');    // 临时设置最大内存占用为3G
set_time_limit(0);   // 设置脚本最大执行时间 为0 永不过期

use common\models\Model as CommonModel;
use common\controllers\AutoHomeSpider;
use admin\modules\sprider\models\get\db\InsertCarAlphabetHome;
use admin\modules\sprider\models\get\db\InsertCarBrandHome;
use admin\modules\sprider\models\get\db\InsertCarVenderHome;
use admin\modules\sprider\models\get\db\InsertCarStyleHome;
use admin\modules\sprider\models\get\db\InsertCarTypeHome;
use Yii;

class GetModel extends CommonModel {

    const ACTION_GETAUTOHOMEDATA = 'action_getautohomedata';

    public function scenarios() {
        return [
            self::ACTION_GETAUTOHOMEDATA => [
            ],
        ];
    }

    public function rules() {
        return [
            [
                ['count_per_page', 'page_num'],
                'integer',
                'min' => 1,
                'tooSmall' => 2004,
                'message' => 2004,
            ],
        ];
    }

    //获取授权列表
    public function actionGetAutoHomeData() {

        try {
            $start = 1;
            $new_url = 'https://www.autohome.com.cn/car/';
            $Api_Visitor = new AutoHomeSpider();
            $html = $Api_Visitor->init($new_url)->getResponse();
            //获取字母
            $search = '/<li><a data-meto="[A-Z]?" data-type="0" href="(.*?)<(\/li)>/si';
            preg_match_all($search, $html, $alphabet_html, PREG_SET_ORDER);
            unset($html);
            $alphabet_len = count($alphabet_html);
            for ($i = 0; $i < $alphabet_len; $i++) {
                $transaction = Yii::$app->db->beginTransaction();
                $a_html = $alphabet_html[$i][0];

                $a_html = preg_replace("/<(\/?li.*?)>/si", "", $a_html);
                $a = preg_replace("/<(\/?a.*?)>/si", "", $a_html);
                unset($a_html);
                $alphabet_data['name'] = $a;
//                print_r($a);die;
//                if (InsertCarAlphabetHome::getField($alphabet_data, 'id')) {
//                    $transaction->commit();
//                    continue;
//                }
                if (!$alphabet_id = InsertCarAlphabetHome::insertData($alphabet_data)) {
                    $transaction->rollback();
                }
                unset($alphabet_data);
                //获取品牌名称
                $brand_url = 'https://www.autohome.com.cn/grade/carhtml/' . $a . '.html';
//                $brand_url = 'https://www.autohome.com.cn/grade/carhtml/' . 'A' . '.html';
                $brand_html = $Api_Visitor->init($brand_url)->getResponse();
                unset($brand_url);
                //获取品牌厂家
                $vender_home_search = '/<dl(.*?)<(\/dl)>/si';
                preg_match_all($vender_home_search, $brand_html, $brand_vender_home, PREG_SET_ORDER);
                unset($brand_html);
                unset($vender_home_search);
                $brand_len = count($brand_vender_home);
                for ($bb = 0; $bb < $brand_len; $bb++) {
                    /**
                     * #############################
                     * 获取品牌                  ###
                     * ############################# 
                     */
                    $brand_vender_home_html = $brand_vender_home[$bb][0];

                    $brand_search = '/<dt(.*?)<a(.*?)<img width="50" height="50" src(.*?)<(\/dt)>(.*?)<dd>(.?)/si';
                    preg_match_all($brand_search, $brand_vender_home_html, $brand_array, PREG_SET_ORDER);
                    unset($brand_search);
                    $brand = $brand_array[0][0];
                    unset($brand_array);
                    //获取图片
                    $logo_img = '/src="(.*?)><\/a>/si';
                    preg_match_all($logo_img, $brand, $logo_img_val, PREG_SET_ORDER);
                    $brand_data['logo_img'] = rtrim($logo_img_val[0][1], '"');
                    //获取品牌名称
                    $brand_name = '/<div>(.*?)<a(.*?)>(.*?)<\/a>/si';
                    preg_match_all($brand_name, $brand, $brand_name_val, PREG_SET_ORDER);
                    unset($brand);
                    unset($brand_name);
                    $brand_data['name'] = $brand_name_val[0][3];
                    $brand_data['alphabet_id'] = $alphabet_id;
                    if (!$brand_id = InsertCarBrandHome::insertData($brand_data)) {
                        $transaction->rollback();
                    }
                    unset($brand_name_val);
                    unset($brand_data);
                    /**
                     * #############################
                     * 获取品牌厂家               ###
                     * ############################# 
                     */
                    $vender_search = '/<div class="h3-tit">(.*?)<\/div>/si';
                    preg_match_all($vender_search, $brand_vender_home_html, $vender, PREG_SET_ORDER);
                    $vender_len = count($vender);
                    for ($cc = 0; $cc < $vender_len; $cc++) {
                        $brand_vender_name = $vender[$cc][1];
                        $vender_data['name'] = $brand_vender_name;
                        $vender_data['alphabet_id'] = $alphabet_id;
                        $vender_data['brand_id'] = $brand_id;
                        if (!$vender_id = InsertCarVenderHome::insertData($vender_data)) {
                            $transaction->rollback();
                        }
                        unset($vender_data);
                        /**
                         * #############################
                         * 获取品牌厂家型号           ###
                         * ############################# 
                         */
//                        $brand_vender_name = '一汽-大众奥迪';
                        $type_search = sprintf('/<div class="h3-tit">%s<\/div>(.*?)<ul class="rank-list-ul(.*?)<\/ul>/si', $this->getNewSring($brand_vender_name));
                        preg_match_all($type_search, $brand_vender_home_html, $type_html, PREG_SET_ORDER);
                        if (!empty($type_html)) {
                            $type_new_search = '/<h4>(.*?)<\/h4>/si';
                            preg_match_all($type_new_search, $type_html[0][0], $type, PREG_SET_ORDER);
                            $type_len = count($type);
                            for ($dd = 0; $dd < $type_len; $dd++) {
//                                $dd = 7;
                                //获取型号名称
                                $type_name_link = $type[$dd][1];
                                $type_name_search = '/<a href="(.*?)">(.*?)<\/a>/si';
                                preg_match_all($type_name_search, $type_name_link, $type_val, PREG_SET_ORDER);

                                $type_name = $type_val[0][2];
                                $type_data['name'] = $type_name;
                                $type_data['alphabet_id'] = $alphabet_id;
                                $type_data['brand_id'] = $brand_id;
                                $type_data['vender_id'] = $vender_id;
                                if (!$type_id = InsertCarTypeHome::insertData($type_data)) {
                                    $transaction->rollback();
                                }
                                unset($type_data);
                                /**
                                 * #############################
                                 * 车款                      ###
                                 * ############################# 
                                 */
                                print_r($start++);
                                $style_link_val = $type_val[0][1];
                                if (strpos($style_link_val, 'class="greylink') !== false) {
                                    //(灰色型号)
                                    $style_grey_link = 'https:' . rtrim($style_link_val, '" class="greylink');
                                    $style_grey_html = $Api_Visitor->init($style_grey_link)->getResponse();
                                    $style_year_grey_search = "/<div class='name'>(.*?)<\/div>/si";
                                    preg_match_all($style_year_grey_search, $style_grey_html, $style_grey, PREG_SET_ORDER);
                                    foreach ($style_grey as $style_grey_value) {
                                        $style_grey_value_html = $style_grey_value[1];
                                        $style_grey_search = "/<a(.*?)>(.*?)<\/a>/si";
                                        preg_match_all($style_grey_search, $style_grey_value_html, $style_grey_name, PREG_SET_ORDER);
                                        $style_grey_data['name'] = $style_grey_name_all = $style_grey_name[0][2]; //车款名
                                        $style_grey_data['year'] = substr($style_grey_name_all, 0, 4); //车款年份
                                        $style_grey_data['alphabet_id'] = $alphabet_id;
                                        $style_grey_data['brand_id'] = $brand_id;
                                        $style_grey_data['vender_id'] = $vender_id;
                                        $style_grey_data['type_id'] = $type_id;
                                        if (!InsertCarStyleHome::insertData($style_grey_data)) {
                                            $transaction->rollback();
                                        }
                                    }
                                } else {//(未灰型号) 
                                    $style_link = 'https:' . $style_link_val;
                                    $style_html = $Api_Visitor->init($style_link)->getResponse();
                                    unset($type_val);
                                    //在售的
                                    $style_sale_search = '/<!--在售 start-->(.*?)<!--在售 end-->/si';
                                    preg_match_all($style_sale_search, $style_html, $style_sale, PREG_SET_ORDER);

                                    if (!empty($style_sale)) {
                                        $style_sale_html = $style_sale[0][0];
                                        unset($style_sale);
                                        $style_sale_search = '/<a href="\/spec\/(.*?)\/#pvareaid=(.*?)">(.*?)<\/a>/si';
                                        preg_match_all($style_sale_search, $style_sale_html, $style_sale_name, PREG_SET_ORDER);
//                                    unset($style_sale);
                                        $style_sale_name_len = count($style_sale_name);
                                        for ($ee = 0; $ee < $style_sale_name_len; $ee++) {
                                            $style_sale_data['name'] = $style_name = $style_sale_name[$ee][3]; //车款名
                                            $style_sale_data['year'] = substr($style_name, 0, 4); //车款年份
                                            $style_sale_data['alphabet_id'] = $alphabet_id;
                                            $style_sale_data['brand_id'] = $brand_id;
                                            $style_sale_data['vender_id'] = $vender_id;
                                            $style_sale_data['type_id'] = $type_id;
                                            if (!InsertCarStyleHome::insertData($style_sale_data)) {
                                                $transaction->rollback();
                                            }
                                        }
                                        unset($style_sale_name);
                                        unset($style_sale_data);
                                    }
                                    //停售
                                    $param_y_search = '/<li><a pageflag="False"(.*?)data="(.*?)"(.*?)><\/li>/si';
                                    preg_match_all($param_y_search, $style_html, $param_y_val, PREG_SET_ORDER);
                                    $param_len = count($param_y_val);

                                    $param_s_search = '/<h3 class="tab-title"><a href="\/(.*?)\/">/si';
                                    preg_match_all($param_s_search, $style_html, $param_s_val, PREG_SET_ORDER);
//                                unset($style_html);
                                    if ($param_s_val) {
                                        $param_s = $param_s_val[0][1];
                                        for ($ff = 0; $ff < $param_len; $ff++) {
                                            $param_y = $param_y_val[$ff][2];
                                            //获取停用数据
                                            $style_link_ajax = sprintf('https://www.autohome.com.cn/ashx/series_allspec.ashx?s=%d&y=%d', $param_s, $param_y);
                                            $style_ajax_result = json_decode($Api_Visitor->init($style_link_ajax)->getResponse(), true);
                                            if ($style_ajax_result) {
                                                $style_shop_ajax = $style_ajax_result['Spec'];
//                                            unset($style_ajax_result);
                                                $style_shop_len = count($style_shop_ajax);

                                                if ($style_shop_len > 0) {
                                                    for ($gg = 0; $gg < $style_shop_len; $gg++) {
                                                        $style_stop_data['name'] = $style_shop_name = $style_shop_ajax[$gg]['Name'];
                                                        $style_stop_data['year'] = substr($style_shop_name, 0, 4); //车款年份
                                                        $style_stop_data['alphabet_id'] = $alphabet_id;
                                                        $style_stop_data['brand_id'] = $brand_id;
                                                        $style_stop_data['vender_id'] = $vender_id;
                                                        $style_stop_data['type_id'] = $type_id;
                                                        if (!InsertCarStyleHome::insertData($style_stop_data)) {
                                                            $transaction->rollback();
                                                        }
                                                    }
                                                    unset($style_shop_ajax);
                                                    unset($style_stop_data);
                                                }
                                            }
                                            unset($style_ajax_result);
                                        }
                                    }
                                    unset($param_y_val);
                                    unset($param_s_val);
                                }
//                             
                            }
                        }
                    }
                }
                unset($type_html);
                unset($brand_vender_home);
                unset($style_html);
                unset($vender);
                $transaction->commit();
            }
        } catch (\Exception $ex) {
            $transaction->rollback();
            echo $ex;
        }
    }

    public function getNewSring($str) {
        $new_str = str_replace('(', '\(', $str);
        $new_str = str_replace(')', '\)', $new_str);
        $new_str = str_replace('-', '\-', $new_str);
        return $new_str;
    }

}
