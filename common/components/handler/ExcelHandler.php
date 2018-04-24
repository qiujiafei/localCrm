<?php
namespace common\components\handler;

use Yii;
use PHPExcel;
use PHPExcel_IOFactory;
use yii\base\Object;

class ExcelHandler extends Handler{

    public static function output(array $rows, array $title = null, string $filename = null){
        if(count($title) > 52)throw new \Exception('too much columns');
        $filename = empty($filename) ? 'default' : $filename;
        $rowName = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
                     'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
        $excel = new PHPExcel;
        $excel->setActiveSheetIndex(0);
        $n = 0;
        $activeSheet = $excel->getActiveSheet();
        if(!empty($title)){
            $rowNum = 1;
            foreach($title as $name){
                $activeSheet->setCellValue($rowName[$n] . $rowNum, $name);
                ++$n;
            }
        }
        $rowNum = empty($title) ? 1 : 2;
        foreach($rows as $row){
            $n = 0;
            if(!is_array($row))throw new \Exception('data must be array');
            if(count($row) > 56)throw new \Exception('too much columns');
            foreach($row as $rowData){
                $activeSheet->setCellValue($rowName[$n] . $rowNum, $rowData);
                ++$n;
            }
            ++$rowNum;
        }
        $excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename=\"{$filename}.xlsx\"");
        header("Content-Transfer-Encoding:binary");
        $excelWriter->save('php://output');
        exit;
    }
}
