<?php
/**
 * CRM system for 9daye
 *
 * @author: qch <qianchaohui@9daye.com.cn>
 */

namespace commodity\modules\member\models\cardTemplate\templates;

/**
 * Abstract role
 */
interface TemplateInterface
{
    public function generate($params);

    public function getOne($id);

    public function getAll($column,$condition,$countPerPage,$pageNum);

    public function setStatus($id);
}