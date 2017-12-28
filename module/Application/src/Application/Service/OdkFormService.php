<?php

namespace Application\Service;

use Application\Service\CommonService;
use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Filter\Compress;
use Zend\Filter\Exception;
use ZipArchive;
use PHPExcel;
use PHPExcel_Cell;
use pData;
use pDraw;
use pRadar;
use pImage;
use pPie;
use TCPDF;

class OdkFormService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    
    public function saveSpiFormVer3($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->saveData($params);
    }
    
    public function getPerformance($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getPerformance($params);
    }
   
    
    public function getPerformanceLast30Days($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getPerformanceLast30Days($params);
    }
   
    public function getPerformanceLast180Days($params = null) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getPerformanceLast180Days();
    }
    
    public function getAllSubmissions($sortOrder = 'DESC') {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getAllSubmissions($sortOrder);
    }
    //export all submissions
    public function exportAllSubmissions($params)
    {
        try{
            $common = new \Application\Service\CommonService();
            $queryContainer = new Container('query');
            $excel = new PHPExcel();
            $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '80MB');
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $output = array();
            $outputScore = array();
            $sheet = $excel->getActiveSheet();
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($dbAdapter);
            if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
                $dateRangeDate = explode(" - ", $params['dateRange']);
                if (isset($dateRangeDate[0]) && trim($dateRangeDate[0]) != "") {
                    $fromDate = $dateRangeDate[0];
                }
                if (isset($dateRangeDate[1]) && trim($dateRangeDate[1]) != "") {
                    $toDate = $dateRangeDate[1];
                }
                if($fromDate == $toDate){
                $displayDate="Date Range : ".$fromDate;
                }else{
                    $displayDate="Date Range : ".$fromDate." to ".$toDate;
                }
            }else{
                $displayDate="";
            }
            $auditRndNo = '';$levelData = '';$affiliation = '';$province = '';$scoreLevel = '';$testPoint = '';
            if (isset($params['auditRndNo']) && ($params['auditRndNo'] != "")) {
                $auditRndNo = "Audit Round No. : ". $params['auditRndNo'];
            }
            if (isset($params['level']) && ($params['level'] != "")) {
                $levelData = "Level : ". $params['level'];
            }
            if (isset($params['affiliation']) && ($params['affiliation'] != "")) {
                $affiliation = "Affiliation : ".$params['affiliation'];
            }
            if (isset($params['province']) && ($params['province'] != "")) {
                $province = "Province/District(s) : ". implode(',',$params['province']);
            }
            if (isset($params['scoreLevel']) && ($params['scoreLevel'] != "")) {
                $scoreLevel = "Score Level : ". $params['scoreLevel'];
            }
            if (isset($params['testPoint']) && ($params['testPoint'] != "")) {
                $testPoint = "Type of Testing Point : ".$params['testPoint'];
            }
            
            $sQueryStr = $sql->getSqlStringForSqlObject($queryContainer->exportAllDataQuery);
            
            $sResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            if(count($sResult) > 0) {
                $auditScore = 0;
                $levelZero = array();
                $levelOne = array();
                $levelTwo = array();
                $levelThree = array();
                $levelFour = array();
                for($l=0;$l<count($sResult);$l++){
                    $row = array();
                    foreach($sResult[$l] as $key=>$aRow) {
                        if($key!='id' && $key!='content' && $key!='token'){
                            
                            if($key=='AUDIT_SCORE_PERCANTAGE'){
                                $auditScore+=$sResult[$l][$key];
                                if($sResult[$l][$key] < 40){
                                    $levelZero[] = $sResult[$l][$key];
                                }
                                else if($sResult[$l][$key] >= 40 && $sResult[$l][$key] < 60){
                                    $levelOne[] = $sResult[$l][$key];
                                }
                                else if($sResult[$l][$key] >= 60 && $sResult[$l][$key] < 80){
                                    $levelTwo[] = $sResult[$l][$key];
                                }
                                else if($sResult[$l][$key] >= 80 && $sResult[$l][$key] < 90){
                                    $levelThree[] = $sResult[$l][$key];
                                }
                                else if($sResult[$l][$key] >= 90){
                                    $levelFour[] = $sResult[$l][$key];
                                }
                            }
                            if($key=='level_other'){
                               $level = " - " .$sResult[$l][$key];
                            }else{
                               $level = '';
                            }
                            if($key=='today'){
                                $sResult[$l][$key] = $common->humanDateFormat($sResult[$l][$key]);
                            }else if($key=='assesmentofaudit'){
                                $sResult[$l][$key] = $common->humanDateFormat($sResult[$l][$key]);
                            }
                            $row[] = $sResult[$l][$key].$level;
                        }
                    }
                    $output[] = $row;
                }
                
                $outputScore['avgAuditScore'] = (count($sResult) > 0) ? round($auditScore/count($sResult),2) : 0;
                $outputScore['levelZeroCount'] = count($levelZero);
                $outputScore['levelOneCount'] = count($levelOne);
                $outputScore['levelTwoCount'] = count($levelTwo);
                $outputScore['levelThreeCount'] = count($levelThree);
                $outputScore['levelFourCount'] = count($levelFour);
            }
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'size'=>12,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    ),
                )
            );
           $borderStyle = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    ),
                )
            );
           
            $sheet->mergeCells('A1:B1');
            $sheet->mergeCells('A2:B2');$sheet->mergeCells('C2:D2');
            $sheet->mergeCells('E2:F2');$sheet->mergeCells('G2:H2');$sheet->mergeCells('I2:J2');$sheet->mergeCells('K2:L2');$sheet->mergeCells('M2:N2');
            $sheet->mergeCells('A4:A5');
            $sheet->mergeCells('B4:B5');
            $sheet->mergeCells('C4:C5');
            $sheet->mergeCells('D4:D5');
            $sheet->mergeCells('E4:E5');
            $sheet->mergeCells('F4:F5');
            $sheet->mergeCells('G4:G5');
            $sheet->mergeCells('H4:H5');
            $sheet->mergeCells('I4:I5');
            
            $sheet->setCellValue('A1', html_entity_decode('Facility Report', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('A2', html_entity_decode($displayDate, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('C2', html_entity_decode($auditRndNo, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('E2', html_entity_decode($levelData, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('G2', html_entity_decode($affiliation, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('I2', html_entity_decode($province, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('K2', html_entity_decode($scoreLevel, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('M2', html_entity_decode($testPoint, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
           
           $colmnNo = 0;
           $rowmnNo = 4;
           $rowmnNo1 = 5;
           foreach($sResult[0] as $key=>$aRow) {
            if($key!='id' && $key!='content' && $key!='token'){
            $cellName = $sheet->getCellByColumnAndRow($colmnNo, $rowmnNo)->getColumn();
            $sheet->mergeCells($cellName.$rowmnNo.':'.$cellName.$rowmnNo1);
            $sheet->setCellValue($cellName.$rowmnNo, html_entity_decode($key, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle($cellName.$rowmnNo.':'.$cellName.$rowmnNo1)->applyFromArray($styleArray);
            $colmnNo++;
            }
           }
            $sheet->getStyle('A1:B1')->getFont()->setBold(TRUE)->setSize(16);
            $sheet->getStyle('A2:B2')->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('C2:D2')->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('E2:F2')->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('G2:H2')->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('I2:J2')->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('K2:L2')->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('M2:N2')->getFont()->setBold(TRUE)->setSize(13);
            
            $start=0;
            foreach ($output as $rowNo => $rowData) {
                $colNo = 0;
                foreach ($rowData as $field => $value) {
                    if (!isset($value)) {
                        $value = "";
                    }
                    if (is_numeric($value)) {
                        $sheet->getCellByColumnAndRow($colNo, $rowNo + 6)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    } else {
                        $sheet->getCellByColumnAndRow($colNo, $rowNo + 6)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $rRowCount = $rowNo + 6;
                    $cellName = $sheet->getCellByColumnAndRow($colNo, $rowNo + 6)->getColumn();
                    $sheet->getStyle($cellName . $rRowCount)->applyFromArray($borderStyle);
                    $sheet->getDefaultRowDimension()->setRowHeight(18);
                    $sheet->getColumnDimensionByColumn($colNo)->setWidth(20);
                    $sheet->getStyleByColumnAndRow($colNo, $rowNo + 6)->getAlignment()->setWrapText(true);
                    $colNo++;
                }
	    }
            $rCount = $rRowCount+3;
            
            $sheet->setCellValue('A'.$rCount, html_entity_decode('No.of Audit(s) : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('B'.$rCount, html_entity_decode(count($sResult)." ", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('A'.$rCount.':B'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            $sheet->setCellValue('C'.$rCount, html_entity_decode('Avg. Audit Score : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('D'.$rCount, html_entity_decode($outputScore['avgAuditScore']." %", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('C'.$rCount.':D'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('E'.$rCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
            $sheet->setCellValue('E'.$rCount, html_entity_decode('Level 0(Below 40) : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('F'.$rCount, html_entity_decode($outputScore['levelZeroCount']." ", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('E'.$rCount.':F'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('G'.$rCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF808000');
            $sheet->setCellValue('G'.$rCount, html_entity_decode('Level 1(40-59) : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('H'.$rCount, html_entity_decode($outputScore['levelOneCount']." ", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('G'.$rCount.':H'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('I'.$rCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            $sheet->setCellValue('I'.$rCount, html_entity_decode('Level 2(60-79) : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('J'.$rCount, html_entity_decode($outputScore['levelTwoCount']." ", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('I'.$rCount.':J'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('K'.$rCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF00FF00');
            $sheet->setCellValue('K'.$rCount, html_entity_decode('Level 3(80-89) : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('L'.$rCount, html_entity_decode($outputScore['levelThreeCount']." ", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('K'.$rCount.':L'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            $sheet->getStyle('M'.$rCount)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF008000');
            $sheet->setCellValue('M'.$rCount, html_entity_decode('Level 4(90) : ', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('N'.$rCount, html_entity_decode($outputScore['levelFourCount']." ", ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getStyle('M'.$rCount.':N'.$rCount)->getFont()->setBold(TRUE)->setSize(13);
            
            $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $filename = 'SPI-RT--CHECKLIST-version-3-' . time() . '.csv';
            $writer->save(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            return $filename;
        }
        catch (Exception $exc) {
            return "";
            error_log("SPI-RT--CHECKLIST-version-3-REPORT-EXCEL--" . $exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function getAllSubmissionsDetails($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        $acl = $this->sm->get('AppAcl');
        return $db->fetchAllSubmissionsDetails($params,$acl);
    }
    public function getAllDuplicateSubmissionsDetails() {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchAllDuplicateSubmissionsDetails();
    }
    
    public function getAllSubmissionsDatas($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        $acl = $this->sm->get('AppAcl');
        return $db->fetchAllSubmissionsDatas($params,$acl);
    }
    //get pending facility names
    public function getPendingFacilityNames()
    {
     $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchPendingFacilityNames();   
    }
    //get all facility names
    public function getAllFacilityNames()
    {
     $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchAllFacilityNames();   
    }
     //merge all facility name
    public function mergeFacilityName($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->mergeFacilityName($params);  
    }
    
    public function getAuditRoundWiseData($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getAuditRoundWiseData($params);
    }
    //download pie chart
    public function getPerformancePieChart($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        $result = $db->getPerformance($params);
        $MyData = new pData();
        if(count($result)>0){
            foreach($result as $key=>$data){
                $MyData->addPoints(array($data['level0'],$data['level1'],$data['level2'],$data['level3'],$data['level4']),"Level".$key);
                $MyData->setSerieDescription("Level".$key);
                $rgbColor = array();
                //Create a loop.
                foreach(array('r', 'g', 'b') as $color){
                    //Generate a random number between 0 and 255.
                    $rgbColor[$color] = mt_rand(0, 255);
                }
                $MyData->setPalette("Level".$key,array("R"=>$rgbColor['r'],"G"=>$rgbColor['g'],"B"=>$rgbColor['b']));
            }
        }
        
        $percentage = $result[0]['level0'] + $result[0]['level1'] + $result[0]['level2'] + $result[0]['level3'] + $result[0]['level4'];
        
        /* Define the absissa serie */
        $MyData->addPoints(array("Level 0 (Below 40)&nbsp;&nbsp;&nbsp;&nbsp;".round(($result[0]['level0'] / $percentage) * 100,1)." %&nbsp;(NO.Of Audits&nbsp;".$result[0]['level0'].")","Level 1 (40-59)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".round(($result[0]['level1'] / $percentage) * 100,1)."%&nbsp;(NO.Of Audits&nbsp;".$result[0]['level1'].")","Level 2 (60-79) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".round(($result[0]['level2'] / $percentage) * 100,1)."%&nbsp;(NO.Of Audits&nbsp;".$result[0]['level2'].")","Level 3 (80-89) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".round(($result[0]['level3'] / $percentage) * 100,1)."%&nbsp;(NO.Of Audits&nbsp;".$result[0]['level3'].")","Level 4 (90 and above) &nbsp;".round(($result[0]['level4'] / $percentage) * 100,1)."%&nbsp;(NO.Of Audits&nbsp;".$result[0]['level4'].")"),"Labels");
        $MyData->setAbscissa("Labels");
       
        /* Create the pChart object */
        $myPicture = new pImage(400,510,$MyData);
        $myPicture->drawRectangle(0,0,390,480,array("R"=>0,"G"=>0,"B"=>0));
        $path = font_path . DIRECTORY_SEPARATOR;
        
        /* Set the default font properties */ 
        $myPicture->setFontProperties(array("FontName"=>$path."/Forgotte.ttf","FontSize"=>13,"R"=>80,"G"=>80,"B"=>80));
       
        /* Enable shadow computing */ 
        $myPicture->setShadow(TRUE,array("X"=>0,"Y"=>0,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>0));
       
        $PieChart = new pPie($myPicture,$MyData);
        $PieChart->draw2DPie(195,195,array("Radius"=>190,"Border"=>TRUE));
        $PieChart->drawPieLegend(5,390);
        $fileName =  'piechart.png';
        $result = $myPicture->autoOutput(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . "piechart.png");
        return $fileName;
    }
    
    //download spider chart pdf
    public function getAuditRoundWiseDataChart($params)
    {
        $db = $this->sm->get('SpiFormVer3Table');
        $result = $db->getAuditRoundWiseData($params);
        $MyData = new pData();
        /* Create and populate the pData object */
        $filename = '';
        if(count($result)>0){
            foreach ($result as $auditNo => $adata){
                //$MyData->addPoints(array(round($adata['PERSONAL_SCORE'],2),round($adata['PHYSICAL_SCORE'],2),round($adata['SAFETY_SCORE'],2),round($adata['PRETEST_SCORE'],2),round($adata['TEST_SCORE'],2),round($adata['POST_SCORE'],2),round($adata['EQA_SCORE'],2)),"Score".$auditNo);
                $MyData->addPoints(array(round($adata['PERSONAL_SCORE'],2),round($adata['PHYSICAL_SCORE'],2),round($adata['SAFETY_SCORE'],2),round($adata['PRETEST_SCORE'],2),round($adata['TEST_SCORE'],2),round($adata['POST_SCORE'],2),round($adata['EQA_SCORE'],2)),"Audit Performance");
                $MyData->setSerieDescription("Audit Performance".$auditNo,$auditNo);
                $rgbColor = array();
                //Create a loop.
                foreach(array('r', 'g', 'b') as $color){
                    //Generate a random number between 0 and 255.
                    $rgbColor[$color] = mt_rand(0, 255);
                }
                $MyData->setPalette("Audit Performance".$auditNo,array("R"=>$rgbColor['r'],"G"=>$rgbColor['g'],"B"=>$rgbColor['b']));
            }
        }
            /* Define the absissa serie */
            $MyData->addPoints(array("Personnel Training & Certification", "Physical", "Safety", "Pre-Testing", "Testing", "Post Testing Phase", "External Quality Audit"),"Label");
            $MyData->setAbscissa("Label");

            /* Create the pChart object */
            $myPicture = new pImage(600,690,$MyData);
            //$myPicture->drawGradientArea(0,0,450,50,DIRECTION_VERTICAL,array("StartR"=>400,"StartG"=>400,"StartB"=>400,"EndR"=>480,"EndG"=>480,"EndB"=>480,"Alpha"=>0));
            //$myPicture->drawGradientArea(0,0,450,25,DIRECTION_HORIZONTAL,array("StartR"=>60,"StartG"=>60,"StartB"=>60,"EndR"=>200,"EndG"=>200,"EndB"=>200,"Alpha"=>0));
            //$myPicture->drawLine(0,25,450,25,array("R"=>255,"G"=>255,"B"=>255));
            //$RectangleSettings = array("R"=>180,"G"=>180,"B"=>180,"Alpha"=>50);
           
            /* Add a border to the picture */
            $myPicture->drawRectangle(0,0,599,678,array("R"=>0,"G"=>0,"B"=>0));
           
            $path = font_path . DIRECTORY_SEPARATOR;
            /* Write the picture title */ 
            //$myPicture->setFontProperties(array("FontName"=>$path."/Silkscreen.ttf","FontSize"=>6));
            //$myPicture->drawText(10,13,"pRadar - Draw radar charts",array("R"=>255,"G"=>255,"B"=>255));
           
            /* Set the default font properties */ 
            $myPicture->setFontProperties(array("FontName"=>$path."/Forgotte.ttf","FontSize"=>15,"R"=>80,"G"=>80,"B"=>80));
           
            /* Enable shadow computing */ 
            $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
           
            /* Create the pRadar object */ 
            $SplitChart = new pRadar();
            /* Draw a radar chart */ 
            $myPicture->setGraphArea(15,15,590,590);
            $Options = array("Layout"=>RADAR_LAYOUT_STAR,"BackgroundGradient"=>array("StartR"=>510,"StartG"=>510,"StartB"=>510,"StartAlpha"=>10,"EndR"=>414,"EndG"=>454,"EndB"=>250,"EndAlpha"=>10), "FontName"=>$path."/pf_arma_five.ttf","FontSize"=>15);
            $SplitChart->drawRadar($myPicture,$MyData,$Options);
           
            /* Write the chart legend */
            $myPicture->setFontProperties(array("FontName"=>$path."/pf_arma_five.ttf","FontSize"=>7));
            $myPicture->drawLegend(330,620,array("Style"=>LEGEND_BOX,"Mode"=>LEGEND_VERTICAL));
           
            /* Render the picture (choose the best way) */
            $fileName =  'radar.png';
            $result = $myPicture->autoOutput(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . "radar.png");
            return $fileName;
    }
    
    public function getFormData($id) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getFormData($id);
    }
    
    public function getSpiV3FormLabels() {
        $db = $this->sm->get('SpiFormLabelsTable');
        return $db->getAllLabels();
    }
    
    public function approveFormStatus($params){
        //\Zend\Debug\Debug::dump(count($params['idList']));die;
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $facilityDb = $this->sm->get('SpiRtFacilitiesTable');
            $db = $this->sm->get('SpiFormVer3Table');
            if(isset($params['idList']) && $params['idList']!='')
            {
                for($i=0;$i<count($params['idList']);$i++){
                $result = $db->updateFormStatus($params['idList'][$i],'approved');
                $facilityDb->addFacilityBasedOnForm($params['idList'][$i]);
                }
            }
            if(isset($params['id'])){
            $result = $db->updateFormStatus($params['id'],'approved');
            $facilityDb->addFacilityBasedOnForm($params['id']);
            }
            if ($result > 0) {
                $adapter->commit();
                return $result;
            }
        } catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function getAllApprovedSubmissions($sortOrder = 'DESC') {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchAllApprovedSubmissions($sortOrder);
    }
    
    public function getAllApprovedSubmissionsTable($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fecthAllApprovedSubmissionsTable($params);
    }
    //export facilty report
    public function exportFacilityReport($params)
    {
        try{
            $common = new \Application\Service\CommonService();
            $queryContainer = new Container('query');
            $excel = new PHPExcel();
            $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '80MB');
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $output = array();
            $sheet = $excel->getActiveSheet();
            $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
            $sql = new Sql($dbAdapter);
            if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
                $dateRangeDate = explode(" - ", $params['dateRange']);
                if (isset($dateRangeDate[0]) && trim($dateRangeDate[0]) != "") {
                    $fromDate = $dateRangeDate[0];
                }
                if (isset($dateRangeDate[1]) && trim($dateRangeDate[1]) != "") {
                    $toDate = $dateRangeDate[1];
                }
                if($fromDate == $toDate){
                $displayDate="Date : ".$fromDate;
                }else{
                    $displayDate="Date : ".$fromDate." to ".$toDate;
                }
            }else{
                $displayDate="";
            }
           
            $sQueryStr = $sql->getSqlStringForSqlObject($queryContainer->exportQuery);
            
            $sResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            if(count($sResult) > 0) {
                
                foreach($sResult as $aRow) {
                    $auditDate="";
                    if(isset($aRow['assesmentofaudit']) && trim($aRow['assesmentofaudit'])!=""){
                        $auditDate=$common->humanDateFormat($aRow['assesmentofaudit']);
                    }
                    $row = array();
                    $row[] = $aRow['facilityname'];
                    $row[] = $auditDate;
                    $row[] = $aRow['testingpointname']. " - " .$aRow['testingpointtype'];
                    $row[] = $aRow['PERSONAL_SCORE'];
                    $row[] = $aRow['PHYSICAL_SCORE'];
                    $row[] = $aRow['SAFETY_SCORE'];
                    $row[] = $aRow['PRETEST_SCORE'];
                    $row[] = $aRow['TEST_SCORE'];
                    $row[] = $aRow['POST_SCORE'];
                    $row[] = $aRow['EQA_SCORE'];
                    $row[] = $aRow['FINAL_AUDIT_SCORE'];
                    $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
                    $output[] = $row;
               }
            }
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'size'=>12,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THICK,
                    ),
                )
            );
           $borderStyle = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    ),
                )
            );
           
            $sheet->mergeCells('A1:B1');
            $sheet->mergeCells('A2:B2');
            $sheet->mergeCells('A4:A5');
            $sheet->mergeCells('B4:B5');
            $sheet->mergeCells('C4:C5');
            $sheet->mergeCells('D4:D5');
            $sheet->mergeCells('E4:E5');
            $sheet->mergeCells('F4:F5');
            $sheet->mergeCells('G4:G5');
            $sheet->mergeCells('H4:H5');
            $sheet->mergeCells('I4:I5');
            $sheet->mergeCells('J4:J5');
            $sheet->mergeCells('K4:K5');
            $sheet->mergeCells('L4:L5');
            
            $sheet->setCellValue('A1', html_entity_decode('Facility Report', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('A2', html_entity_decode($displayDate, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
           
            $sheet->setCellValue('A4', html_entity_decode('Facility name', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('B4', html_entity_decode('Audit Date', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('C4', html_entity_decode('Testing Point', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('D4', html_entity_decode('Personnel Training & Certification', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('E4', html_entity_decode('Physical', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('F4', html_entity_decode('Safety', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('G4', html_entity_decode('Pre-Testing', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('H4', html_entity_decode('Testing', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('I4', html_entity_decode('Post-Testing', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('J4', html_entity_decode('External QA', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('K4', html_entity_decode('Total', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('L4', html_entity_decode('% Scores', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            
            
            $sheet->getStyle('A1:B1')->getFont()->setBold(TRUE)->setSize(16);
            $sheet->getStyle('A2:B2')->getFont()->setBold(TRUE)->setSize(13);
            
            $sheet->getStyle('A4:A5')->applyFromArray($styleArray);
            $sheet->getStyle('B4:B5')->applyFromArray($styleArray);
            $sheet->getStyle('C4:C5')->applyFromArray($styleArray);
            $sheet->getStyle('D4:D5')->applyFromArray($styleArray);
            $sheet->getStyle('E4:E5')->applyFromArray($styleArray);
            $sheet->getStyle('F4:F5')->applyFromArray($styleArray);
            $sheet->getStyle('G4:G5')->applyFromArray($styleArray);
            $sheet->getStyle('H4:H5')->applyFromArray($styleArray);
            $sheet->getStyle('I4:I5')->applyFromArray($styleArray);
            $sheet->getStyle('J4:J5')->applyFromArray($styleArray);
            $sheet->getStyle('K4:K5')->applyFromArray($styleArray);
            $sheet->getStyle('L4:L5')->applyFromArray($styleArray);
            
            $start=0;
            foreach ($output as $rowNo => $rowData) {
                $colNo = 0;
                foreach ($rowData as $field => $value) {
                    if (!isset($value)) {
                        $value = "";
                    }
                    if (is_numeric($value)) {
                        $sheet->getCellByColumnAndRow($colNo, $rowNo + 6)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    } else {
                        $sheet->getCellByColumnAndRow($colNo, $rowNo + 6)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $rRowCount = $rowNo + 6;
                    $cellName = $sheet->getCellByColumnAndRow($colNo, $rowNo + 6)->getColumn();
                    $sheet->getStyle($cellName . $rRowCount)->applyFromArray($borderStyle);
                    $sheet->getDefaultRowDimension()->setRowHeight(18);
                    $sheet->getColumnDimensionByColumn($colNo)->setWidth(20);
                    $sheet->getStyleByColumnAndRow($colNo, $rowNo + 6)->getAlignment()->setWrapText(true);
                    $colNo++;
                }
	    }
	    
            $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $filename = 'facility-report-' . date('d-M-Y-H-i-s') . '.xls';
            $writer->save(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            return $filename;
        }
        catch (Exception $exc) {
            return "";
            error_log("GENERATE-FACILITY-REPORT-EXCEL--" . $exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function getAllApprovedSubmissionLocation($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchAllApprovedSubmissionLocation($params);
    }
    
    public function getZeroQuestionCounts($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getZeroQuestionCounts($params);
    }
    
    public function getAllApprovedTestingVolume($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getAllApprovedTestingVolume($params);
    }
    
    public function getSpiV3PendingCount() {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->getSpiV3PendingCount();
    }
    
    public function updateSpiForm($params){
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $db = $this->sm->get('SpiFormVer3Table');
            $result = $db->updateSpiFormDetails($params);
            if ($result > 0) {
                $adapter->commit();
                $container = new Container('alert');
                $container->alertMsg = 'Form details updated successfully';
                return $result;
            }
        } catch (Exception $exc) {
            $adapter->rollBack();
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    //get all audit round no
    public function getSpiV3FormAuditNo(){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchSpiV3FormAuditNo();
    }
    
    public function getFacilitiesAudits($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchFacilitiesAudits($params);
    }
    
    public function deleteAuditData($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->deleteAuditRowData($params);
    }
    
    public function getSpiV3FormFacilityAuditNo($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchSpiV3FormFacilityAuditNo($params);
    }
    
    public function getAllApprovedSubmissionsDetailsBasedOnAuditDate($params) {
        $db = $this->sm->get('SpiFormVer3Table');
        $acl = $this->sm->get('AppAcl');
        return $db->fetchAllApprovedSubmissionsDetailsBasedOnAuditDate($params,$acl);
    }
    
    public function getSpiV3FormUniqueTokens(){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchSpiV3FormUniqueTokens();
    }
    
    public function getViewDataDetails($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchViewDataDetails($params);
    }
    
    public function getAllTestingPointType(){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchAllTestingPointType();
    }
    
    public function getTestingPointTypeNamesByType($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchTestingPointTypeNamesByType($params);
    }
    
    public function getSpiV3FormUniqueLevelNames(){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchSpiV3FormUniqueLevelNames();
    }
    public function getSpiV3FormUniqueDistrict(){
        $db = $this->sm->get('SpiRtFacilitiesTable');
        return $db->getSpiV3FormUniqueDistrict();
    }
    public function getDistrictData($params){
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->fetchDistrictData($params);
    }
    
    public function addDownloadData($params){
        $db = $this->sm->get('SpiFormVer3DownloadTable');
        return $db->addDownloadDataDetails($params);
    }
    
    public function getDownloadDataList(){
        $common = new CommonService();
        $db = $this->sm->get('SpiFormVer3DownloadTable');
        $result = $db->fetchDownloadDataList();
        if(count($result['formResult']) >0){
            //get config details
            $globalDb = $this->sm->get('GlobalTable');
            $configData = $globalDb->getGlobalConfig();  
            $configFile = CONFIG_PATH . DIRECTORY_SEPARATOR . "label.php";
            $fileContents=file_get_contents($configFile);
            //Convert the JSON string back into an array.
            $decoded = json_decode($fileContents, true);
            $language = $configData['language'];
            foreach($result['formResult'] as $formData){
                // create new PDF document
                $pdf = new TcpdfExtends(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->setSchemeName(ucwords($configData['header']),$configData['logo']);
                // set document information
                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Nicola Asuni');
                $pdf->SetTitle('SPI-RT Checklist');
                $pdf->SetSubject('TCPDF Tutorial');
                $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
                
                // set default header data
                $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
                
                // set header and footer fonts
                $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                
                // set default monospaced font
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                
                // set margins
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                
                // set auto page breaks
                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                
                // set image scale factor
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                
                // set some language-dependent strings (optional)
                if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                    require_once(dirname(__FILE__).'/lang/eng.php');
                    $pdf->setLanguageArray($l);
                }
                // ---------------------------------------------------------
                
                // set font
                $pdf->SetFont('times', '', 10);
                
                // add a page
                $pdf->AddPage();
                //$pdf->SetY(20,true,false);
                $partA='<p style="font-weight:bold;line-height:24px;">'.$decoded[$language]['/SPI_RT/TESTSITE/FACILITY/info2:label'].'</p>';
                //$partA.='<br/>';
                
                $pdf->writeHTML($partA, true, 0, true, 0);
                
                $pdf->writeHTMLCell('',12,'','','<p>'.$decoded[$language]['/SPI_RT/TESTSITE/FACILITY/info2:hint'].'</p>',0,1,false,true,'L',true);
                if($language=='Portuguese'){
                    $langDateFormat='(dd/mm/aaaa)';
                }else if($language=='Spanish'){
                    $langDateFormat='(dd/mm/aaaa)';
                }
                else{
                    $langDateFormat='(dd/mm/yyyy)';
                }
                $testingTab='<table border="1" cellspacing="0" cellpadding="5">';
                
                $testingTab.='<tr>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/assesmentofaudit:label'].'</b>'.$langDateFormat.': '.$common->humanDateFormat($formData['assesmentofaudit']).'</td>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/auditroundno:label'].'</b> '.$formData['auditroundno'].'</td>';
                $testingTab.='</tr>';
                
                $testingTab.='<tr>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/facilityname:label'].'</b> '.$formData['facilityname'].'</td>';
                if($language=='Portuguese'){
                  $testingTab.='<td><b>Identificacao do local de testagem </b>(se aplicavel): '.$formData['facilityid'].'</td>';
                }else if($language=='Spanish'){
                  $testingTab.='<td><b>Tipo de sitio de pruebas </b>(seleccione uno): '.$formData['facilityid'].'</td>';
                }
                else{
                  $testingTab.='<td><b>Testing Facility ID</b>(if applicable) : '.$formData['facilityid'].'</td>';	
                }
                $testingTab.='</tr>';
                
                $testingTab.='<tr>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/testingpointname:label'].'</b> '.$formData['testingpointname'].'</td>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/testingpointtype:label'].'</b> '.$formData['testingpointtype'];
                $testingTab.=((isset($formData['testingpointtype_other']) && $formData['testingpointtype_other'] != "") ? " - " . $formData['testingpointtype_other']: "").'</td>';
                $testingTab.='</tr>';
                
                $testingTab.='<tr>';
                $testingTab.='<td colspan="2"><b>'.$decoded[$language]['/SPI_RT/TESTSITE/locationaddress:label'].'</b> '.$formData['locationaddress'].'</td>';
                $testingTab.='</tr>';
                
                $testingTab.='<tr>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/level:label'].'</b> <br/>'.$formData['level'];
                $testingTab.=((isset($formData['level_other']) && $formData['level_other'] != "") ? " Other - " . $formData['level_other'] : "").':'.$formData['level_name'];
                $testingTab.='</td>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/affiliation:label'].'</b><br/>'.$formData['affiliation'];
                $testingTab.=((isset($formData['affiliation_other']) && $formData['affiliation_other'] != "") ? " Other : " . $formData['affiliation_other'] : "");
                $testingTab.='</td>';
                $testingTab.='</tr>';
                
                $testingTab.='<tr>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/NumberofTester:label'].'</b>'.$formData['NumberofTester'].'</td>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/avgMonthTesting:label'].'</b>'.$formData['avgMonthTesting'].'</td>';
                $testingTab.='</tr>';
                
                $testingTab.='<tr>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/name_auditor_lead:label'].'</b>'.$formData['name_auditor_lead'].'</td>';
                $testingTab.='<td><b>'.$decoded[$language]['/SPI_RT/TESTSITE/name_auditor2:label'].'</b>'.$formData['name_auditor2'].'</td>';
                $testingTab.='</tr>';
                
                $testingTab.='</table>';
                
                $pdf->writeHTML($testingTab, true, 0, true, 0);
                
                $partBHeading='<b>'.$decoded[$language]['/SPI_RT/SPIRT/info4:label'].'</b>';
                
                $pdf->writeHTML($partBHeading, true, 0, true, 0);
                
                
                $partBCont='<br/><div>'.$decoded[$language]['/SPI_RT/SPIRT/info4:hint'].'</div>';
                
                $pdf->writeHTML($partBCont, true, 0, true, 0);
                
                $partBTable='<table border="1" cellspacing="0" cellpadding="5" style="width:100%;">';
                $partBTable.="<tr>";
                $language;
                if($language=='Portuguese'){
                        $partBTable.='<td style="text-align:center;font-weight:bold;width:52%;">SECÇÃO</td>';
                }
                else{
                        $partBTable.='<td style="text-align:center;font-weight:bold;width:52%;">SECTION</td>';
                }
                
                $partBTable.='<td style="text-align:center;font-weight:bold;width:7%;">'.$decoded[$language]['/SPI_RT/PERSONAL/PER_G_1_10/PERSONAL_Q_1_10/1:label'].'</td>';
                $partBTable.='<td style="text-align:center;font-weight:bold;width:8%;">'.$decoded[$language]['/SPI_RT/PERSONAL/PER_G_1_3/PERSONAL_Q_1_3/0.5:label'].'</td>';
                $partBTable.='<td style="text-align:center;font-weight:bold;width:7%;">'.$decoded[$language]['/SPI_RT/PERSONAL/PER_G_1_8/PERSONAL_Q_1_8/0:label'].'</td>';
                $partBTable.='<td style="text-align:center;font-weight:bold;width:18%;">'.$decoded[$language]['/SPI_RT/PERSONAL/PER_G_1_5/PERSONAL_C_1_5:label'].'</td>';
                if($language=='Portuguese'){
                        $partBTable.='<td style="text-align:center;font-weight:bold;width:8%;">Pontuação</td>';
                }
                elseif($language=='Spanish'){
                        $partBTable.='<td style="text-align:center;font-weight:bold;width:8%;">Punteo</td>';
                }
                else{
                        $partBTable.='<td style="text-align:center;font-weight:bold;width:8%;">Score</td>';	
                }
                
                $partBTable.='</tr>';
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/PERSONAL:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">10</td>';
                $partBTable.='</tr>';
                
                    for($i=1;$i<11;$i++)
                    {
                        $partBTable.='<tr>';
                        
                        $partBTable.='<td style="width:52%;">'.$decoded[$language]['/SPI_RT/PERSONAL/PER_G_1_'.$i.'/PERSONAL_Q_1_'.$i.':label'].'</td>';
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['PERSONAL_Q_1_'.$i]) && $formData['PERSONAL_Q_1_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['PERSONAL_Q_1_'.$i]) && $formData['PERSONAL_Q_1_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['PERSONAL_Q_1_'.$i]) && $formData['PERSONAL_Q_1_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td>'.($formData['PERSONAL_C_1_'.$i]).'</td>';
                        $partBTable.='<td style="text-align:center;">'.($formData['PERSONAL_Q_1_'.$i]).'</td>';
                        $partBTable.='</tr>';
                    }
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/PERSONAL/PERSONAL_Display:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['PERSONAL_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/PHYSICAL:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">5</td>';
                $partBTable.='</tr>';
                
                    for($i=1;$i<6;$i++)
                    {
                        
                        $partBTable.='<tr>';
                        
                        $partBTable.='<td>'.$decoded[$language]['/SPI_RT/PHYSICAL/PHY_G_2_'.$i.'/PHYSICAL_Q_2_'.$i.':label'].'</td>';
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['PHYSICAL_Q_2_'.$i]) && $formData['PHYSICAL_Q_2_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['PHYSICAL_Q_2_'.$i]) && $formData['PHYSICAL_Q_2_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['PHYSICAL_Q_2_'.$i]) && $formData['PHYSICAL_Q_2_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td>'.($formData['PHYSICAL_C_2_'.$i]).'</td>';
                        $partBTable.='<td style="text-align:center;">'.($formData['PHYSICAL_Q_2_'.$i]).'</td>';
                        $partBTable.='</tr>';
                
                    }
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/PHYSICAL/PHYSICAL_Display:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['PHYSICAL_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/SAFETY:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">11</td>';
                $partBTable.='</tr>';
                
                    for($i=1;$i<12;$i++)
                    {
                        $partBTable.='<tr>';
                        
                        $partBTable.='<td>'.$decoded[$language]['/SPI_RT/SAFETY/SAF_3_'.$i.'/SAFETY_Q_3_'.$i.':label'].'</td>';
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['SAFETY_Q_3_'.$i]) && $formData['SAFETY_Q_3_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['SAFETY_Q_3_'.$i]) && $formData['SAFETY_Q_3_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['SAFETY_Q_3_'.$i]) && $formData['SAFETY_Q_3_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td>'.($formData['SAFETY_C_3_'.$i]).'</td>';
                        $partBTable.='<td style="text-align:center;">'.($formData['SAFETY_Q_3_'.$i]).'</td>';
                        $partBTable.='</tr>';
                    }
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/SAFETY/SAFETY_DISPLAY:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['SAFETY_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/PRETEST:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">12</td>';
                $partBTable.='</tr>';
                    
                    for($i=1;$i<13;$i++)
                    {
                    $partBTable.='<tr>';
                    
                    $partBTable.='<td>'.$decoded[$language]['/SPI_RT/PRETEST/PRE_4_'.$i.'/PRE_Q_4_'.$i.':label'].'</td>';
                    $partBTable.='<td style="text-align:center;">';
                    $partBTable.=(isset($formData['PRE_Q_4_'.$i]) && $formData['PRE_Q_4_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                    $partBTable.='</td>';
                    
                    $partBTable.='<td style="text-align:center;">';
                    $partBTable.=(isset($formData['PRE_Q_4_'.$i]) && $formData['PRE_Q_4_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                    $partBTable.='</td>';
                    
                    $partBTable.='<td style="text-align:center;">';
                    $partBTable.=(isset($formData['PRE_Q_4_'.$i]) && $formData['PRE_Q_4_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                    $partBTable.='</td>';
                    
                    $partBTable.='<td>'.($formData['PRE_C_4_'.$i]).'</td>';
                    $partBTable.='<td style="text-align:center;">'.($formData['PRE_Q_4_'.$i]).'</td>';
                    $partBTable.='</tr>';
                    }
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/PRETEST/PRETEST_Display:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['PRETEST_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/TEST:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">9</td>';
                $partBTable.='</tr>';
                
                    for($i=1;$i<10;$i++)
                    {
                        $partBTable.='<tr>';
                        
                        $partBTable.='<td>'.$decoded[$language]['/SPI_RT/TEST/TEST_5_'.$i.'/TEST_Q_5_'.$i.':label'].'</td>';
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['TEST_Q_5_'.$i]) && $formData['TEST_Q_5_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['TEST_Q_5_'.$i]) && $formData['TEST_Q_5_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['TEST_Q_5_'.$i]) && $formData['TEST_Q_5_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td>'.($formData['TEST_C_5_'.$i]).'</td>';
                        $partBTable.='<td style="text-align:center;">'.($formData['TEST_Q_5_1']).'</td>';
                        $partBTable.='</tr>';
                    }
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/TEST/TEST_DISPLAY:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['TEST_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/POSTTEST:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">9</td>';
                $partBTable.='</tr>';
                
                    for($i=1;$i<10;$i++)
                    {
                    $partBTable.='<tr>';
                    
                    $partBTable.='<td>'.$decoded[$language]['/SPI_RT/POSTTEST/POST_6_'.$i.'/POST_Q_6_'.$i.':label'].'</td>';
                    $partBTable.='<td style="text-align:center;">';
                    $partBTable.=(isset($formData['POST_Q_6_'.$i]) && $formData['POST_Q_6_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                    $partBTable.='</td>';
                    
                    $partBTable.='<td style="text-align:center;">';
                    $partBTable.=(isset($formData['POST_Q_6_'.$i]) && $formData['POST_Q_6_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                    $partBTable.='</td>';
                    
                    $partBTable.='<td style="text-align:center;">';
                    $partBTable.=(isset($formData['POST_Q_6_'.$i]) && $formData['POST_Q_6_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                    $partBTable.='</td>';
                    
                    $partBTable.='<td>'.($formData['POST_C_6_'.$i]).'</td>';
                    $partBTable.='<td style="text-align:center;">'.($formData['POST_Q_6_'.$i]).'</td>';
                    $partBTable.='</tr>';
                    }
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/POSTTEST/POST_DISPLAY:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['POST_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/EQA:label'].'</td>';
                //$partBTable.='<td style="text-align:center;font-weight:bold;background-color:#dddbdb;">8/14</td>';
                $partBTable.='</tr>';
                
                    for($i=1;$i<9;$i++)
                    {
                        $partBTable.='<tr>';
                        
                        $partBTable.='<td>'.$decoded[$language]['/SPI_RT/EQA/EQA_7_'.$i.'/EQA_Q_7_'.$i.':label'].'</td>';
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['EQA_Q_7_'.$i]) && $formData['EQA_Q_7_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['EQA_Q_7_'.$i]) && $formData['EQA_Q_7_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['EQA_Q_7_'.$i]) && $formData['EQA_Q_7_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td>'.($formData['EQA_C_7_'.$i]).'</td>';
                        $partBTable.='<td style="text-align:center;">'.($formData['EQA_Q_7_'.$i]).'</td>';
                        $partBTable.='</tr>';
                    }
                
                $partBTable.='<tr>';
                $partBTable.='<td colspan="6" style="text-align:center;font-weight:bold;background-color:#dddbdb;">'.$decoded[$language]['/SPI_RT/EQA/sampleretesting:label'].'</td>';
                $partBTable.='</tr>';
                
                    for($i=9;$i<15;$i++)
                    {
                        $partBTable.='<tr>';
                        
                        $partBTable.='<td>'.$decoded[$language]['/SPI_RT/EQA/SAMPLEREF/EQA_7_'.$i.'/EQA_Q_7_'.$i.':label'].'</td>';
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['EQA_Q_7_'.$i]) && $formData['EQA_Q_7_'.$i] == 1 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['EQA_Q_7_'.$i]) && $formData['EQA_Q_7_'.$i] == 0.5 ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td style="text-align:center;">';
                        $partBTable.=(isset($formData['EQA_Q_7_'.$i]) && $formData['EQA_Q_7_'.$i] == "0" ) ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "";
                        $partBTable.='</td>';
                        
                        $partBTable.='<td>'.($formData['EQA_C_7_'.$i]).'</td>';
                        $partBTable.='<td style="text-align:center;">'.($formData['EQA_Q_7_'.$i]).'</td>';
                        $partBTable.='</tr>';
                    }
                $partBTable.='<tr>';
                $partBTable.='<td colspan="5" style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/EQA/EQA_DISPLAY:label'].'</td>';
                $partBTable.='<td style="text-align:center;">'.$formData['EQA_SCORE'].'</td>';
                $partBTable.='</tr>';
                
                $partBTable.='</table>';
                if($language=='Portuguese'){
                $partBTable.='<p>*A area marcada com asteriscos so é aplicavel para os locais onde as amostras retestadas sao executadas.</p>';
                }
                else if($language=='Spanish'){
                $partBTable.='<p>*Lo que aparece marcado con un asterisco son solo aplicables a sitios donde la repetición de las pruebas se hace.</p>';
                }else{
                $partBTable.='<p>*Those marked with an asterisk are only applicable to sites where sample retesting is performed.</p>';	
                }
                
                $pdf->writeHTML($partBTable, true, 0, true, 0);
                
                $partC='<br/><p style="font-weight:bold;">'.$decoded[$language]['/SPI_RT/scoring/info5:label'].'</p>';
                $partC.='<br/><span>'.$decoded[$language]['/SPI_RT/scoring/info6:label'].'</span>';
                $partC.='<p>'.$decoded[$language]['/SPI_RT/scoring/info10:label'].'</p>';
                $partC.='<p>'.$decoded[$language]['/SPI_RT/scoring/info11:label'].'</p>';
                
                $pdf->writeHTML($partC, true, 0, true, 0);
                
                $summaryExp=explode(PHP_EOL,$decoded[$language]['/SPI_RT/SUMMARY/info17:label']);
                $totPointScored='';
                $totExpectScored='';
                $perScored='';
                if(isset($summaryExp[8]) && trim($summaryExp[8])!=""){
                        $totPointScored=$summaryExp[8];
                }
                if(isset($summaryExp[9]) && trim($summaryExp[9])!=""){
                        $totExpectScored=$summaryExp[9];
                }
                if(isset($summaryExp[10]) && trim($summaryExp[10])!=""){
                        $expPerScored=explode("=",$summaryExp[10]);
                        $perScored=(string) $expPerScored[0];
                }
                
                $partCTable='<table border="1" cellspacing="0" cellpadding="5">';
                
                $partCTable.='<tr style="font-weight:bold;text-align:center;">';
                if($language=='Portuguese'){
                $partCTable.='<td style="width:15%">NIVEL</td>';
                }
                else if($language=='Spanish'){
                $partCTable.='<td style="width:15%">Nivel</td>';
                }else{
                $partCTable.='<td style="width:15%">Levels</td>';	
                }
                
                
                if($language=='Portuguese'){
                $partCTable.='<td  style="width:25%">PONTUACAO EM %</td>';
                $partCTable.='<td  style="width:60%">DESCRIÇAO DOS RESULTADOS</td>';
                }
                else if($language=='Spanish'){
                $partCTable.='<td  style="width:25%">% Puntaje</td>';
                $partCTable.='<td  style="width:60%">Descripción de los resultados</td>';
                }
                else{
                $partCTable.='<td  style="width:25%">'.$perScored.'</td>';
                $partCTable.='<td  style="width:60%">Description of results</td>';	
                }
                $partCTable.='</tr>';
                
                $level0=explode("-",$decoded[$language]['/SPI_RT/SUMMARY/info21:label']);
                $level1=explode("-",$decoded[$language]['/SPI_RT/SUMMARY/info22:label']);
                if(count($level1)>2){
                        $level1[1]=$level1[1]." - ".$level1[2];
                }
                $level2=explode("-",$decoded[$language]['/SPI_RT/SUMMARY/info23:label']);
                if(count($level2)>2){
                        $level2[1]=$level2[1]." - ".$level2[2];
                }
                $level3=explode("-",$decoded[$language]['/SPI_RT/SUMMARY/info24:label']);
                if(count($level3)>2){
                        $level3[1]=$level3[1]." - ".$level3[2];
                }
                $level4=explode("-",$decoded[$language]['/SPI_RT/SUMMARY/info25:label']);
                
                if($language=='Spanish'){
                        $level0[0]="Nivel 0";
                        $level0[1]="Menos de 40% ";
                        $level1[0]="Nivel 1";
                        $level2[0]="Nivel 2";
                        $level3[0]="Nivel 3";
                        $level4[0]="Nivel 4";
                        $level4[1]="90% a más";
                }
                
                $partCTable.='<tr>';
                $partCTable.='<td style="background-color:#C00000;">'.$level0[0].'</td>';
                $partCTable.='<td>'.$level0[1].'</td>';
                if($language=='Portuguese'){
                $partCTable.='<td>Necessidade de melhoria em todas as areas e remediaçoes imediatas</td>';
                }
                else if($language=='Spanish'){
                $partCTable.='<td>Necesita mejorar en todas las áreas y es necesaria corrección inmediata</td>';
                }else{
                $partCTable.='<td>Needs improvement in all areas and immediate remediation</td>';	
                }
                
                $partCTable.='</tr>';
                
                $partCTable.='<tr>';
                $partCTable.='<td style="background-color:#E36C0A;">'.$level1[0].'</td>';
                $partCTable.='<td>'.$level1[1].'</td>';
                if($language=='Portuguese'){
                $partCTable.='<td>Necessidade de melhorias em areas especificas</td>';
                }
                else if($language=='Spanish'){
                        $partCTable.='<td>Necesita mejorar en áreas específicas</td>';
                }
                else{
                $partCTable.='<td>Needs improvement in specific areas</td>';	
                }
                $partCTable.='</tr>';
                
                $partCTable.='<tr>';
                $partCTable.='<td style="background-color:#FFFF00;">'.$level2[0].'</td>';
                $partCTable.='<td>'.$level2[1].'</td>';
                if($language=='Portuguese'){
                $partCTable.='<td>Parcialmente admissivel ou aceitavel</td>';
                }
                else if($language=='Spanish'){
                        $partCTable.='<td>Parcialmente elegible</td>';
                }
                else{
                $partCTable.='<td>Partially eligible</td>';	
                }
                $partCTable.='</tr>';
                
                $partCTable.='<tr>';
                $partCTable.='<td style="background-color:#92D050;">'.$level3[0].'</td>';
                $partCTable.='<td>'.$level3[1].'</td>';
                if($language=='Portuguese'){
                $partCTable.='<td>Proximo da certificaçao nacional</td>';
                }
                else if($language=='Spanish'){
                $partCTable.='<td>Cercano a sitio nacional certificado</td>';
                }
                else{
                $partCTable.='<td>Close to national site certification</td>';	
                }
                $partCTable.='</tr>';
                
                $partCTable.='<tr>';
                $partCTable.='<td style="background-color:#00B050;">'.$level4[0].'</td>';
                $partCTable.='<td>'.$level4[1].'</td>';
                if($language=='Portuguese'){
                $partCTable.='<td>Admissivel a certificaçao nacional</td>';
                }
                else if($language=='Spanish'){
                $partCTable.='<td>Elegible para ser certificado</td>';
                }
                else{
                $partCTable.='<td>Eligible to national site certification</td>';	
                }
                $partCTable.='</tr>';
                
                $partCTable.='</table>';
                
                $pdf->writeHTML($partCTable,true,0,true,0);
                $summationExp=explode(PHP_EOL,$decoded[$language]['/SPI_RT/SUMMARY/info12:label']);
                $facilityName='';
                if(isset($summationExp[0]) && trim($summationExp[0])!=""){
                $heading=$summationExp[0];	
                }
                if(isset($summationExp[2]) && trim($summationExp[2])!=""){
                $facilityName=$summationExp[2];
                }
                if(isset($summationExp[3]) && trim($summationExp[3])!=""){
                $auditorName=$summationExp[3];
                }
                if(isset($summationExp[4]) && trim($summationExp[4])!=""){
                  $textPointName=$summationExp[4];
                }
                $staffAuditedName = '';
                $noOfTester = '';
                if(isset($summationExp[5]) && trim($summationExp[5])!= ""){
                    $expStaffAuditedName=explode(":",$summationExp[5]);
                    $staffAuditedName=$expStaffAuditedName[0];
                    $noOfTester=$expStaffAuditedName[1];
                }
                if($language=='Spanish'){
                    $heading="PARTE D: Informe resumido del evaluador de la auditoría SPI-RT";
                }
                
                $partDTitle='<p style="font-weight:bold;line-height:30px;">'.$heading.'</p>';
                $pdf->writeHTML($partDTitle,true,0,true,0);
                
                $partDtableBox1='<table cellspacing="0" cellpadding="2">';
                $partDtableBox1.="<tr><td>".$facilityName.$formData['facilityname']."</td></tr>";
                
                $partDtableBox1.="<tr><td>";
                if($language=='Portuguese'){
                $partDtableBox1.="Tipo de local:";
                }
                else if($language=='Spanish'){
                $partDtableBox1.="Tipo de sitio:";
                }
                else{
                $partDtableBox1.="Site Type:";
                }
                $partDtableBox1.=$formData['testingpointtype'];
                
                $partDtableBox1.=((isset($formData['testingpointtype_other']) && $formData['testingpointtype_other'] != "") ? " - " . $formData['testingpointtype_other']: "");
                $partDtableBox1.="</td></tr>";
                $partDtableBox1.="<tr><td>".$staffAuditedName.":".$formData['staffaudited']."</td></tr>";
                $partDtableBox1.='</table>';
                $pdf->writeHTMLCell(50,26,'','',$partDtableBox1,1, 0, 0, true, 'L');
                
                $partDtableBox2='<table cellspacing="0" cellpadding="5">';
                $partDtableBox2.="<tr><td>".$noOfTester.": ".$formData['NumberofTester']."</td></tr><tr><td>".$decoded[$language]['/SPI_RT/durationaudit:label'].$formData['durationaudit']."</td></tr>";
                $partDtableBox2.='</table>';
                
                $pdf->writeHTMLCell(50,26,70,'',$partDtableBox2,1,0, 0, true, 'L',true);
                
                $scorePer=round($formData['AUDIT_SCORE_PERCANTAGE']);
                $level='';
                $colorCode='';
                if($scorePer<40){
                  $level=$level0[0];
                  //$level="Level 0";
                  $colorCode="background-color:#C00000";
                }elseif($scorePer>=40 && $scorePer<=59){
                  //$level="Level 1";
                  $level=$level1[0];
                  $colorCode="background-color:#E36C0A";
                }elseif($scorePer>=60 && $scorePer<=79){
                  //$level="Level 2";
                  $level=$level2[0];
                  $colorCode="background-color:#FFFF00";
                }elseif($scorePer>=80 && $scorePer<=89){
                  //$level="Level 3";
                  $level=$level3[0];
                  $colorCode="background-color:#92D050";
                }elseif($scorePer>=90){
                  //$level="Level 4";
                  $level=$level4[0];
                  $colorCode="background-color:#00B050";
                }
                                                          
                $partDtableBox3='<table cellspacing="0" cellpadding="5">';
                $partDtableBox3.="<tr><td>".$totPointScored.$formData['FINAL_AUDIT_SCORE']."</td></tr>";
                $partDtableBox3.="<tr><td>".$totExpectScored.$formData['MAX_AUDIT_SCORE']."</td></tr>";
                $partDtableBox3.='<tr><td>'.$perScored."= ".round($formData['AUDIT_SCORE_PERCANTAGE'],2).'% &nbsp; <span style="'.$colorCode.'">  &nbsp;&nbsp;'.$level.'  &nbsp;&nbsp;</span></td></tr>';
                $partDtableBox3.='</table>';
                
                $pdf->writeHTMLCell(70,26,125,'',$partDtableBox3,1,1, 0, true, 'L',true);
                
                // set recommend
                $recommend = explode("-",$decoded[$language]['/SPI_RT/correctiveaction/action:label']);
                $timeLine = explode("-",$decoded[$language]['/SPI_RT/correctiveaction/timeline:label']);
                
                $partDTable='<br/><br/><table border="1" cellspacing="0" cellpadding="5" style="width:100%">';
                $partDTable.='<tr>';
                $partDTable.='<td rowspan="2" style="font-weight:bold;text-align:center;width:9%"><br/><br/>'.$decoded[$language]['/SPI_RT/correctiveaction/sectionno:label'].'</td>';
                $partDTable.='<td rowspan="2" style="font-weight:bold;text-align:center;width:22%"><br/><br/>'.$decoded[$language]['/SPI_RT/correctiveaction/deficiency:label'].'</td>';
                $partDTable.='<td colspan="2" style="font-weight:bold;text-align:center;width:20%">'.$decoded[$language]['/SPI_RT/correctiveaction/correction:label'].'</td>';
                $partDTable.='<td rowspan="2" style="font-weight:bold;text-align:center;width:23%">'.$decoded[$language]['/SPI_RT/correctiveaction/auditorcomment:label'].'</td>';
                $partDTable.='<td colspan="2" style="font-weight:bold;text-align:center;width:26%">'.$recommend[0].'</td>';
                $partDTable.='</tr>';
                $partDTable.='<tr>';
                $partDTable.='<td style="font-weight:bold;text-align:center;width:12%;">'.$decoded[$language]['/SPI_RT/correctiveaction/correction/Immediate:label'].'</td>';
                $partDTable.='<td style="font-weight:bold;text-align:center;width:8%;">'.$decoded[$language]['/SPI_RT/correctiveaction/correction/Followup:label'].'</td>';
                $partDTable.='<td style="font-weight:bold;text-align:center;">'.$recommend[1].'</td>';
                $partDTable.='<td style="font-weight:bold;text-align:center;">'.$timeLine[1].'</td>';
                $partDTable.='</tr>';
                if(isset($formData['correctiveaction']) && $formData['correctiveaction'] != ""  && $formData['correctiveaction'] != "[]"){
                    $correctiveActions = json_decode($formData['correctiveaction'],true);
                    foreach($correctiveActions as $ca) {
                        $partDTable.='<tr>';
                        $partDTable.='<td style="text-align:center;">'.$ca['sectionno'].'</td>';
                        $partDTable.='<td>'.$ca['deficiency'].'</td>';
                        $partDTable.='<td style="text-align:center;">';
                        $partDTable.=($ca['correction'] == 'Immediate' ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "" );
                        $partDTable.='</td>';
                        $partDTable.='<td style="text-align:center;">';
                        $partDTable.=($ca['correction'] == 'Followup' ? '<img src="'.APPLICATION_PATH. DIRECTORY_SEPARATOR.'img'. DIRECTORY_SEPARATOR.'black-tick.png" width="20">' : "" );
                        $partDTable.='</td>';
                        $partDTable.='<td>'.$ca['auditorcomment'].'</td>';
                        $partDTable.='<td>'.$ca['action'].'</td>';
                        $partDTable.='<td>'.$ca['timeline'].'</td>';
                        $partDTable.='</tr>';
                    }
                }else {
                    $partDTable.='<tr>';
                    $partDTable.='<td colspan="7">'.$decoded[$language]['/SPI_RT/PERSONAL/PER_G_1_8/PERSONAL_Q_1_8/0:label'].' '.$decoded[$language]['SPI_RT/correctiveaction:label'].'</td>';
                    $partDTable.='</tr>';
                }
                $partDTable.='</table><br/><br/><br/>';
                $pdf->writeHTML($partDTable,true,0,true,0);
                
                $signBox1='<table cellspacing="0" cellpadding="4">';
                $signBox1.='<tr><td>'.$decoded[$language]['/SPI_RT/staffaudited:label'].'</td></tr>';
                $signBox1.='<tr><td>'.$decoded[$language]['/SPI_RT/personincharge:label'].$formData["personincharge"].'</td></tr>';
                $signBox1.='</table>';
                $pdf->writeHTMLCell(90,18,'','',$signBox1,1, 0, 0, true, 'L');
                
                $signBox2='<table cellspacing="0" cellpadding="4">';
                
                if($language=='Spanish'){
                $signBox2.='<tr><td>Nombre y firma del auditor:</td></tr>';
                }else{
                $signBox2.='<tr><td>'.$decoded[$language]['/SPI_RT/SUMMARY/info26:label'].'</td></tr>';	
                }
                
                if($language=='Portuguese'){
                $signBox2.="<tr><td>Date ".$langDateFormat.":</td></tr>";
                }
                else if($language=='Spanish'){
                $signBox2.="<tr><td>Fecha ".$langDateFormat.":</td></tr>";
                }
                else{
                $signBox2.="<tr><td>Date ".$langDateFormat.":</td></tr>";	
                }
                $signBox2.='</table>';
                $pdf->writeHTMLCell(80,18,115,'',$signBox2,1,1, 0, true, 'L');
                //Close and output PDF document
                $fileName = "SPI-RT-CHECKLIST-".date('d-M-Y-H-i-s').".pdf";
                if(!file_exists(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download")){
                    mkdir(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download");
                }
                if(!file_exists(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download". DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id)){
                    mkdir(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download". DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id);
                }
                $filePath = TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download". DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id . DIRECTORY_SEPARATOR . $fileName;
                $pdf->Output($filePath, "F");
                //============================================================+
                // END OF FILE
                //============================================================+
            }
           //zip part
            $zip = new ZipArchive();
            $filename = TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download" . DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id.'.zip';
            if($zip->open($filename, ZipArchive::CREATE)== TRUE) {
                $file_list = scandir(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download". DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id);
                if(count($file_list) >2){
                    foreach ($file_list as $file) {
                      if (in_array($file, array(".",".."))) continue;
                      $zip->addFile(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download". DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id . DIRECTORY_SEPARATOR . $file, $file);
                    }
                }
            }
            $zip->close();
            //zip end
            //remove source pdf(s)
            $common->removeDirectory(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR. "download". DIRECTORY_SEPARATOR . $result['downloadResult']->r_download_id);
        }
    }
    
    public function removeAudit($params){
        $db = $this->sm->get('SpiFormVer3DuplicateTable');
        return $db->removeAuditData($params);
    }
    
    public function getDownloadFilesRow(){
        $db = $this->sm->get('SpiFormVer3DownloadTable');
        return $db->fetchDownloadFilesRow();
    }
    
    public function validateSPIV3File($params){
        $db = $this->sm->get('SpiFormVer3TempTable');
        $dbMain = $this->sm->get('SpiFormVer3Table');
        $dbAdapter = $this->sm->get('Zend\Db\Adapter\Adapter');
        $sql = new Sql($dbAdapter);
        $fileName = preg_replace('/[^A-Za-z0-9.]/', '-', $_FILES['fileName']['name']);
        $fileName = str_replace(" ", "-", $fileName);
        $ranNumber = str_pad(rand(0, pow(10, 6)-1), 6, '0', STR_PAD_LEFT);
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileName =$ranNumber.".".$extension;
        if (!file_exists(UPLOAD_PATH) && !is_dir(UPLOAD_PATH)) {
            mkdir(APPLICATION_PATH . DIRECTORY_SEPARATOR . "uploads");
        }
        if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "import-files") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "import-file")) {
            mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "import-files");
        }
        if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR ."import-files" . DIRECTORY_SEPARATOR . $fileName)) {
            if (move_uploaded_file($_FILES['fileName']['tmp_name'], UPLOAD_PATH . DIRECTORY_SEPARATOR ."import-files" . DIRECTORY_SEPARATOR . $fileName)) {
                $db->delete('1');
                $objPHPExcel = \PHPExcel_IOFactory::load(UPLOAD_PATH . DIRECTORY_SEPARATOR ."import-files" . DIRECTORY_SEPARATOR . $fileName);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                $highestColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);
                $rownumber = 1;
                $row = $objPHPExcel->getActiveSheet()->getRowIterator($rownumber)->current();
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $header = explode(":",$cell->getValue());
                    $headerName[] = end($header);
                }
                $count = count($sheetData);
                $findInstancePosition = array_search('instanceID', $headerName);
                $findStartPosition = array_search('start', $headerName);
                $findEndPosition = array_search('end', $headerName);
                $findAuditSignPosition = array_search('auditorSignature', $headerName);
                $findAssesmentOfAuditPosition = array_search('assesmentofaudit', $headerName);
                
                for ($i = 2; $i <= $count; $i++) {
                    $row = $objPHPExcel->getActiveSheet()->getRowIterator($i)->current();
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $inc = 0;
                    $validateData = 0;
                    foreach ($cellIterator as $cell) {
                        $value = $cell->getValue();
                        if($inc==$findStartPosition || $inc==$findEndPosition && trim($cell->getValue())!=''){
                            $dValue = explode(" ",trim($cell->getValue()));
                            if(count($dValue)==2){
                            $value = trim($cell->getValue());    
                            }else{
                            $originalDate = $dValue[5]."-".$dValue[1]."-".$dValue[2];
                            $newDate = date("Y-m-d", strtotime($originalDate));
                            $value = $newDate."T".$dValue[3].".000+02";
                            }
                        }
                        if($inc==$findAssesmentOfAuditPosition){
                            if(trim($cell->getValue())!=''){
                                $dValue = explode(" ",trim($cell->getValue()));
                                if(count($dValue)==2){
                                    $value = trim($cell->getValue());
                                }else{
                                $originalDate = $dValue[5]."-".$dValue[1]."-".$dValue[2];
                                $newDate = date("Y-m-d", strtotime($originalDate));
                                $value = $newDate;
                                }
                            }else{
                                $value = '0000:00:00';
                            }
                        }else if($inc==$findAuditSignPosition && trim($cell->getValue()!='')){
                            $auditorSign = array('url'=>$cell->getValue());
                            $value = json_encode($auditorSign);
                        }
                        if($inc == $findInstancePosition){
                            $validateQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->where(array('instanceID'=>trim($cell->getValue())));
                            $validateQueryStr = $sql->getSqlStringForSqlObject($validateQuery);
                            $validateResult = $dbAdapter->query($validateQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
                            if($validateResult){
                                $validateData = 1;//exist meta instance id
                            }else{
                                $validateData = 0;//new meta instance id
                            }
                        }
                        $spiv3FormData[$headerName[$inc]] = $value;
                        $inc++;
                    }
                    $spiv3FormData['spi_data_status'] = $validateData;                    
                    $db->insert($spiv3FormData);
                }
            }
        }
    }
    public function getAllValidateSpiv3Details($params) {
        $db = $this->sm->get('SpiFormVer3TempTable');
        return $db->fetchAllValidateSpiv3Details($params);
    }
    public function addValidateSpiv3Data($params)
    {
        $db = $this->sm->get('SpiFormVer3Table');
        return $db->addValidateSpiv3Data($params);
    }
}