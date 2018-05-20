<?php

namespace app\models;

/**
 * Class ExcelHelper
 * @package app\service
 */
class ExcelHelper {

    /**
     * 导出excel
     * @param $filename
     * @param $sheetName
     * @param $title
     * @param $data
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public static function exportExcel($filename, $sheetName, $title, $data)
    {
        require_once('../vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
        //设置缓存方式
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        \PHPExcel_Settings::setCacheStorageMethod ( $cacheMethod );

        $objPHPExcel = new \PHPExcel();

        $colNum = 1;
        $rowNum = 1;
        $sheetNum = 0;

        $objPHPExcel->setActiveSheetIndex ( $sheetNum );
        // Rename sheet
        $objPHPExcel->getActiveSheet ()->setTitle ( $sheetName );

        foreach ( $title as $title_value ) {
            $colNumAlpha = self::dec2alpha ( $colNum );
            $colName = $colNumAlpha . $rowNum;
            //获取cell样式
            $curStyle = $objPHPExcel->getActiveSheet ()->getStyle ( $colName );
            //设置字体
            $curStyle->getFont ()->setName ( 'Arial' );
            $curStyle->getFont ()->setSize ( 12 );
            $curStyle->getFont ()->setBold ( true );
            //设置居中
            $curStyle->getAlignment ()->setHorizontal ( \PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $curStyle->getAlignment ()->setVertical ( \PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $objPHPExcel->getActiveSheet ()->setCellValue ( $colName,  ( $title_value ) );
            $colNum ++;
        }
        $rowNum ++;
        // add data
        foreach ( $data as $row ) {
            $colNum = 1;
            foreach ( $row as $value ) {
                $colNumAlpha = self::dec2alpha ( $colNum );
                $colName = $colNumAlpha . $rowNum;
                $objPHPExcel->getActiveSheet ()->setCellValue ($colName, $value);
                $colNum ++;
            }

            $rowNum ++;

        }
//        if(count($data) == 0){
//            $objPHPExcel->getActiveSheet ()->setCellValue ( 'A2', '未找到数据' );
//        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex ( 0 );

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 将数字转换为excel列号
     * @param $dec
     * @return bool
     */
    static function dec2alpha($dec) {
        if ($dec < 1) {
            return false;
        }

        $dec --;
        $alpha = '';
        $results = array ();
        $times = 1;
        do {
            $results [] = ($times ++ == 1) ? $dec % 26 : $dec % 26 - 1;
        } while ( $dec = floor ( $dec / 26 ) );

        for($i = count ( $results ) - 1; $i >= 0; $i --) {
            $alpha .= chr ( $results [$i] + 97 );
        }

        return strtoupper ( $alpha );
    }

}
