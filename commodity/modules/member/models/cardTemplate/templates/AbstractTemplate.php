<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\cardtemplate\templates;

/**
 * Abstract role
 */
class AbstractTemplate
{

    protected $_template;
    
    protected $id;

    protected $name;

    protected $recharge_money;

    public function loadTemplate(TemplateInterface $template){
        if($template instanceof AbstractTemplate){
            $this->_template = $template;
        }else{
            throw new \Exception('添加失败',12001);
        }
    }
    
    public function generate($params){
        return $this->_template->generate($params);
    }

    public function getOne($id){
        return $this->_template->getOne($id);
    }

    public function getAll($column,$condition,$countPerPage,$pageNum){
        return $this->_template->genAll($column,$condition,$countPerPage,$pageNum);
    }

    public function setStatus($id){
        return $this->_template->setStatus($id);
    }

    public function putService($id, $serviceList){
        return $this->_template->putService($id, $serviceList);
    }

    public function getService($id){
        return $this->_template->getService($id);
    }

    public function getCheckService($id){
        $template = $this->getOne($id);
        $storeId = $template['store_id'];
        $checkList = $this->_template->getService($id);
        $checkId = array_column($checkList,'service_id');
        $allList = [];
        foreach ($allList as $k => &$v){
            if (in_array($v['id'], $checkId)){
                $v['if_check'] = true;
            }else{
                $v['if_check'] = false;
            }
        }
        return $allList;
    }
    
    public static function loadModel($model, $params){
        foreach($params as $key=>$value){
            $model[$key] = $value;
        }
        return $model;
    }

    public function handleList($list, $fixItem, $item){
        $data = [];
        foreach($list as $key => $value){
            $item_arr = $item;
            foreach($item_arr as $k => &$v){
                $v = $value[$k]??$v;
            }
            $merge_arr = array_merge($fixItem,$item_arr);
            $data[] = $merge_arr;
        }
        return $data;
    }

}