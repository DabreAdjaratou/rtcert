<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Model\SpiRtFacilitiesTable;
use Application\Model\GlobalTable;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Countries
 *
 * @author amit
 */
class SpiFormVer3Table extends AbstractTableGateway {

    protected $table = 'spi_form_v_3';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function saveData($params) {
        
        if($params == null || $params == "" || (is_array($params) && count($params) ==0 )){
            exit;
        }
        
        $sql = new Sql($this->adapter);
        $insert = $sql->insert('form_dump');
        $d = array('data_dump' => json_encode($params) , 'received_on' => new \Zend\Db\Sql\Expression("NOW()"));
        $dbAdapter = $this->adapter;
        $insert->values($d);
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $results = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
       
       //get global values
       $globalDB = new GlobalTable($dbAdapter);
       $globalValue = $globalDB->getGlobalValue('approve_status');
       if($globalValue=='yes')
       {
        $approveStatus = 'approved';
       }else{
        $approveStatus = 'pending';
       }
       
        //error_log(print_r($params,true));

        foreach($params['data'] as $datar){
            $par = array();
            $data = array();
            foreach($datar as $key=>$val){
               $key = preg_replace('/[\*]+/', '', $key);
                $key = str_replace("lab_geopoint:","",$key);
                if(is_array($val)){
                    $val = json_encode($val);
                }
                $data[$key] = $val."";
            
            }
        
            try{
               
        
        $sql = new Sql($this->adapter);
    
    
        $insert = $sql->insert('spi_form_v_3');
        if(isset($data['testingpointname']) && trim($data['testingpointname'])==""){
            $data['testingpointname']=$data['testingpointtype'];
        }
        
        
        $data['instanceID'] = isset($data['instanceID']) ? $data['instanceID'] :"";
        $data['instanceName'] = isset($data['instanceName']) ? $data['instanceName'] :"";
        
        $par = array(
                'token' => $params['token'],
                'content' => $params['content'],
                'formId' => $params['formId'],
                'formVersion' => $params['formVersion'],
                'meta-instance-id' => $data['meta-instance-id'],
                'meta-model-version' => $data['meta-model-version'],
                'meta-ui-version' => $data['meta-ui-version'],
                'meta-submission-date' => $data['meta-submission-date'],
                'meta-is-complete' => $data['meta-is-complete'],
                'meta-date-marked-as-complete' => $data['meta-date-marked-as-complete'],
                'start' => $data['start'],
                'end' => $data['end'],
                'today' => $data['today'],
                'deviceid' => $data['deviceid'],
                'subscriberid' => $data['subscriberid'],
                'text_image' => $data['text_image'],
                'info1' => $data['info1'],
                'info2' => $data['info2'],
                'assesmentofaudit' => $data['assesmentofaudit'],
                'auditroundno' => $data['auditroundno'],
                'facilityname' => $data['facilityname'],
                'facilityid' => $data['facilityid'],
                'testingpointname' => $data['testingpointname'],
                'testingpointtype' => $data['testingpointtype'],
                'testingpointtype_other' => $data['testingpointtype_other'],
                'locationaddress' => $data['locationaddress'],
                'level' => $data['level'],
                'level_other' => $data['level_other'],
                'level_name' => $data['level_name'],
                'affiliation' => $data['affiliation'],
                'affiliation_other' => $data['affiliation_other'],
                'NumberofTester' => (isset($data['NumberofTester']) && $data['NumberofTester'] > 0 ? $data['NumberofTester'] : 0),
                'avgMonthTesting' => (isset($data['avgMonthTesting']) && $data['avgMonthTesting'] > 0 ? $data['avgMonthTesting'] : 0),
                'name_auditor_lead' => $data['name_auditor_lead'],
                'name_auditor2' => $data['name_auditor2'],
                'info4' => $data['info4'],
                'INSTANCE' => $data['INSTANCE'],
                'PERSONAL_Q_1_1' => $data['PERSONAL_Q_1_1'],
                'PERSONAL_C_1_1' => $data['PERSONAL_C_1_1'],
                'PERSONAL_Q_1_2' => $data['PERSONAL_Q_1_2'],
                'PERSONAL_C_1_2' => $data['PERSONAL_C_1_2'],
                'PERSONAL_Q_1_3' => $data['PERSONAL_Q_1_3'],
                'PERSONAL_C_1_3' => $data['PERSONAL_C_1_3'],
                'PERSONAL_Q_1_4' => $data['PERSONAL_Q_1_4'],
                'PERSONAL_C_1_4' => $data['PERSONAL_C_1_4'],
                'PERSONAL_Q_1_5' => $data['PERSONAL_Q_1_5'],
                'PERSONAL_C_1_5' => $data['PERSONAL_C_1_5'],
                'PERSONAL_Q_1_6' => $data['PERSONAL_Q_1_6'],
                'PERSONAL_C_1_6' => $data['PERSONAL_C_1_6'],
                'PERSONAL_Q_1_7' => $data['PERSONAL_Q_1_7'],
                'PERSONAL_C_1_7' => $data['PERSONAL_C_1_7'],
                'PERSONAL_Q_1_8' => $data['PERSONAL_Q_1_8'],
                'PERSONAL_C_1_8' => $data['PERSONAL_C_1_8'],
                'PERSONAL_Q_1_9' => $data['PERSONAL_Q_1_9'],
                'PERSONAL_C_1_9' => $data['PERSONAL_C_1_9'],
                'PERSONAL_Q_1_10' => $data['PERSONAL_Q_1_10'],
                'PERSONAL_C_1_10' => $data['PERSONAL_C_1_10'],
                'PERSONAL_SCORE' => $data['PERSONAL_SCORE'],
                'PERSONAL_Display' => $data['PERSONAL_Display'],
                'PERSONALPHOTO' => $data['PERSONALPHOTO'],
                'PHYSICAL_Q_2_1' => $data['PHYSICAL_Q_2_1'],
                'PHYSICAL_C_2_1' => $data['PHYSICAL_C_2_1'],
                'PHYSICAL_Q_2_2' => $data['PHYSICAL_Q_2_2'],
                'PHYSICAL_C_2_2' => $data['PHYSICAL_C_2_2'],
                'PHYSICAL_Q_2_3' => $data['PHYSICAL_Q_2_3'],
                'PHYSICAL_C_2_3' => $data['PHYSICAL_C_2_3'],
                'PHYSICAL_Q_2_4' => $data['PHYSICAL_Q_2_4'],
                'PHYSICAL_C_2_4' => $data['PHYSICAL_C_2_4'],
                'PHYSICAL_Q_2_5' => $data['PHYSICAL_Q_2_5'],
                'PHYSICAL_C_2_5' => $data['PHYSICAL_C_2_5'],
                'PHYSICAL_SCORE' => $data['PHYSICAL_SCORE'],
                'PHYSICAL_Display' => $data['PHYSICAL_Display'],
                'PHYSICALPHOTO' => $data['PHYSICALPHOTO'],
                'SAFETY_Q_3_1' => $data['SAFETY_Q_3_1'],
                'SAFETY_C_3_1' => $data['SAFETY_C_3_1'],
                'SAFETY_Q_3_2' => $data['SAFETY_Q_3_2'],
                'SAFETY_C_3_2' => $data['SAFETY_C_3_2'],
                'SAFETY_Q_3_3' => $data['SAFETY_Q_3_3'],
                'SAFETY_C_3_3' => $data['SAFETY_C_3_3'],
                'SAFETY_Q_3_4' => $data['SAFETY_Q_3_4'],
                'SAFETY_C_3_4' => $data['SAFETY_C_3_4'],
                'SAFETY_Q_3_5' => $data['SAFETY_Q_3_5'],
                'SAFETY_C_3_5' => $data['SAFETY_C_3_5'],
                'SAFETY_Q_3_6' => $data['SAFETY_Q_3_6'],
                'SAFETY_C_3_6' => $data['SAFETY_C_3_6'],
                'SAFETY_Q_3_7' => $data['SAFETY_Q_3_7'],
                'SAFETY_C_3_7' => $data['SAFETY_C_3_7'],
                'SAFETY_Q_3_8' => $data['SAFETY_Q_3_8'],
                'SAFETY_C_3_8' => $data['SAFETY_C_3_8'],
                'SAFETY_Q_3_9' => $data['SAFETY_Q_3_9'],
                'SAFETY_C_3_9' => $data['SAFETY_C_3_9'],
                'SAFETY_Q_3_10' => $data['SAFETY_Q_3_10'],
                'SAFETY_C_3_10' => $data['SAFETY_C_3_10'],
                'SAFETY_Q_3_11' => $data['SAFETY_Q_3_11'],
                'SAFETY_C_3_11' => $data['SAFETY_C_3_11'],
                'SAFETY_SCORE' => $data['SAFETY_SCORE'],
                'SAFETY_DISPLAY' => $data['SAFETY_DISPLAY'],
                'SAFETYPHOTO' => $data['SAFETYPHOTO'],
                'PRE_Q_4_1' => $data['PRE_Q_4_1'],
                'PRE_C_4_1' => $data['PRE_C_4_1'],
                'PRE_Q_4_2' => $data['PRE_Q_4_2'],
                'PRE_C_4_2' => $data['PRE_C_4_2'],
                'PRE_Q_4_3' => $data['PRE_Q_4_3'],
                'PRE_C_4_3' => $data['PRE_C_4_3'],
                'PRE_Q_4_4' => $data['PRE_Q_4_4'],
                'PRE_C_4_4' => $data['PRE_C_4_4'],
                'PRE_Q_4_5' => $data['PRE_Q_4_5'],
                'PRE_C_4_5' => $data['PRE_C_4_5'],
                'PRE_Q_4_6' => $data['PRE_Q_4_6'],
                'PRE_C_4_6' => $data['PRE_C_4_6'],
                'PRE_Q_4_7' => $data['PRE_Q_4_7'],
                'PRE_C_4_7' => $data['PRE_C_4_7'],
                'PRE_Q_4_8' => $data['PRE_Q_4_8'],
                'PRE_C_4_8' => $data['PRE_C_4_8'],
                'PRE_Q_4_9' => $data['PRE_Q_4_9'],
                'PRE_C_4_9' => $data['PRE_C_4_9'],
                'PRE_Q_4_10' => $data['PRE_Q_4_10'],
                'PRE_C_4_10' => $data['PRE_C_4_10'],
                'PRE_Q_4_11' => $data['PRE_Q_4_11'],
                'PRE_C_4_11' => $data['PRE_C_4_11'],
                'PRE_Q_4_12' => $data['PRE_Q_4_12'],
                'PRE_C_4_12' => $data['PRE_C_4_12'],
                'PRETEST_SCORE' => $data['PRETEST_SCORE'],
                'PRETEST_Display' => $data['PRETEST_Display'],
                'PRETESTPHOTO' => $data['PRETESTPHOTO'],
                'TEST_Q_5_1' => $data['TEST_Q_5_1'],
                'TEST_C_5_1' => $data['TEST_C_5_1'],
                'TEST_Q_5_2' => $data['TEST_Q_5_2'],
                'TEST_C_5_2' => $data['TEST_C_5_2'],
                'TEST_Q_5_3' => $data['TEST_Q_5_3'],
                'TEST_C_5_3' => $data['TEST_C_5_3'],
                'TEST_Q_5_4' => $data['TEST_Q_5_4'],
                'TEST_C_5_4' => $data['TEST_C_5_4'],
                'TEST_Q_5_5' => $data['TEST_Q_5_5'],
                'TEST_C_5_5' => $data['TEST_C_5_5'],
                'TEST_Q_5_6' => $data['TEST_Q_5_6'],
                'TEST_C_5_6' => $data['TEST_C_5_6'],
                'TEST_Q_5_7' => $data['TEST_Q_5_7'],
                'TEST_C_5_7' => $data['TEST_C_5_7'],
                'TEST_Q_5_8' => $data['TEST_Q_5_8'],
                'TEST_C_5_8' => $data['TEST_C_5_8'],
                'TEST_Q_5_9' => $data['TEST_Q_5_9'],
                'TEST_C_5_9' => $data['TEST_C_5_9'],
                'TEST_SCORE' => $data['TEST_SCORE'],
                'TEST_DISPLAY' => $data['TEST_DISPLAY'],
                'TESTPHOTO' => $data['TESTPHOTO'],
                'POST_Q_6_1' => $data['POST_Q_6_1'],
                'POST_C_6_1' => $data['POST_C_6_1'],
                'POST_Q_6_2' => $data['POST_Q_6_2'],
                'POST_C_6_2' => $data['POST_C_6_2'],
                'POST_Q_6_3' => $data['POST_Q_6_3'],
                'POST_C_6_3' => $data['POST_C_6_3'],
                'POST_Q_6_4' => $data['POST_Q_6_4'],
                'POST_C_6_4' => $data['POST_C_6_4'],
                'POST_Q_6_5' => $data['POST_Q_6_5'],
                'POST_C_6_5' => $data['POST_C_6_5'],
                'POST_Q_6_6' => $data['POST_Q_6_6'],
                'POST_C_6_6' => $data['POST_C_6_6'],
                'POST_Q_6_7' => $data['POST_Q_6_7'],
                'POST_C_6_7' => $data['POST_C_6_7'],
                'POST_Q_6_8' => $data['POST_Q_6_8'],
                'POST_C_6_8' => $data['POST_C_6_8'],
                'POST_Q_6_9' => $data['POST_Q_6_9'],
                'POST_C_6_9' => $data['POST_C_6_9'],
                'POST_SCORE' => $data['POST_SCORE'],
                'POST_DISPLAY' => $data['POST_DISPLAY'],
                'POSTTESTPHOTO' => $data['POSTTESTPHOTO'],
                'EQA_Q_7_1' => $data['EQA_Q_7_1'],
                'EQA_C_7_1' => $data['EQA_C_7_1'],
                'EQA_Q_7_2' => $data['EQA_Q_7_2'],
                'EQA_C_7_2' => $data['EQA_C_7_2'],
                'EQA_Q_7_3' => $data['EQA_Q_7_3'],
                'EQA_C_7_3' => $data['EQA_C_7_3'],
                'EQA_Q_7_4' => $data['EQA_Q_7_4'],
                'EQA_C_7_4' => $data['EQA_C_7_4'],
                'EQA_Q_7_5' => $data['EQA_Q_7_5'],
                'EQA_C_7_5' => $data['EQA_C_7_5'],
                'EQA_Q_7_6' => $data['EQA_Q_7_6'],
                'EQA_C_7_6' => $data['EQA_C_7_6'],
                'EQA_Q_7_7' => $data['EQA_Q_7_7'],
                'EQA_C_7_7' => $data['EQA_C_7_7'],
                'EQA_Q_7_8' => $data['EQA_Q_7_8'],
                'EQA_C_7_8' => $data['EQA_C_7_8'],
                'sampleretesting' => $data['sampleretesting'],
                'EQA_Q_7_9' => $data['EQA_Q_7_9'],
                'EQA_C_7_9' => $data['EQA_C_7_9'],
                'EQA_Q_7_10' => $data['EQA_Q_7_10'],
                'EQA_C_7_10' => $data['EQA_C_7_10'],
                'EQA_Q_7_11' => $data['EQA_Q_7_11'],
                'EQA_C_7_11' => $data['EQA_C_7_11'],
                'EQA_Q_7_12' => $data['EQA_Q_7_12'],
                'EQA_C_7_12' => $data['EQA_C_7_12'],
                'EQA_Q_7_13' => $data['EQA_Q_7_13'],
                'EQA_C_7_13' => $data['EQA_C_7_13'],
                'EQA_Q_7_14' => $data['EQA_Q_7_14'],
                'EQA_C_7_14' => $data['EQA_C_7_14'],
                'EQA_MAX_SCORE' => $data['EQA_MAX_SCORE'],
                'EQA_REQ' => $data['EQA_REQ'],
                'EQA_OPT' => $data['EQA_OPT'],
                'EQA_SCORE' => $data['EQA_SCORE'],
                'EQA_DISPLAY' => $data['EQA_DISPLAY'],
                'EQAPHOTO' => $data['EQAPHOTO'],
                'FINAL_AUDIT_SCORE' => $data['FINAL_AUDIT_SCORE'],
                'MAX_AUDIT_SCORE' => $data['MAX_AUDIT_SCORE'],
                'AUDIT_SCORE_PERCANTAGE' => $data['AUDIT_SCORE_PERCANTAGE'],
                'staffaudited' => $data['staffaudited'],
                'durationaudit' => $data['durationaudit'],
                'personincharge' => $data['personincharge'],
                'endofsurvey' => $data['endofsurvey'],
                'info5' => $data['info5'],
                'info6' => $data['info6'],
                'info10' => $data['info10'],
                'info11' => $data['info11'],
                'summarypage' => $data['summarypage'],
                'SUMMARY_NOT_AVL' => $data['SUMMARY_NOT_AVL'],
                'info12' => $data['info12'],
                'info17' => $data['info17'],
                'info21' => $data['info21'],
                'info22' => $data['info22'],
                'info23' => $data['info23'],
                'info24' => $data['info24'],
                'info25' => $data['info25'],
                'info26' => $data['info26'],
                'info27' => $data['info27'],
                'correctiveaction' => $data['correctiveaction'],
                'sitephoto' => $data['sitephoto'],
                'Latitude' => $data['Latitude'],
                'Longitude' => $data['Longitude'],
                'Altitude' => $data['Altitude'],
                'Accuracy' => $data['Accuracy'],
                'auditorSignature' => $data['auditorSignature'],
                'instanceID' => $data['instanceID'],
                'instanceName' => $data['instanceName'],
                'status'=>$approveStatus
            );
            $dbAdapter = $this->adapter;
            $insert->values($par);
            $selectString = $sql->getSqlStringForSqlObject($insert);
            //error_log($selectString);
            $results = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);        
            
            if($approveStatus=='approved'){
                $facilityDb = new SpiRtFacilitiesTable($dbAdapter);
                $facilityResult = $facilityDb->addFacilityBasedOnForm($results->getGeneratedValue());
            }
            
            }catch(Exception $e){
                error_log($e->getMessage());
                error_log( $e->getTraceAsString());
            }
             
        }
    }
    
    
    public function getPerformance($params){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->columns(array(
                                                'oldestDate' => new \Zend\Db\Sql\Expression("MIN(`assesmentofaudit`)"),
                                                'newestDate' => new \Zend\Db\Sql\Expression("MAX(`assesmentofaudit`)"),
                                                'totalDataPoints' => new \Zend\Db\Sql\Expression("COUNT(*)"),
                                                'level0' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) < 40, 1,0))"),
                                                'level1' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 40 and (AUDIT_SCORE_PERCANTAGE) < 60, 1,0))"),
                                                'level2' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 60 and (AUDIT_SCORE_PERCANTAGE) < 80, 1,0))"),
                                                'level3' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 80 and (AUDIT_SCORE_PERCANTAGE) < 90, 1,0))"),
                                                'level4' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 90, 1,0))"),
                                                ))
                                ->where(array('spiv3.status'=>'approved'));
        if(isset($params['fieldName']) && trim($params['fieldName'])!= ''){
            $sQuery = $sQuery->where(array($params['fieldName']=>$params['val']));
        }
        
        if(isset($params['roundno']) && $params['roundno']!=''){
            $sQuery = $sQuery->where('spiv3.auditroundno IN ("' . implode('", "', $params['roundno']) . '")');
        }
        $start_date = '';
        $end_date = '';
        if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
            $dateField = explode(" ", $params['dateRange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        
        if(isset($params['testPoint']) && trim($params['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$params['testPoint']."'");
            if(isset($params['testPointName']) && trim($params['testPointName'])!= ''){
                 if(trim($params['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$params['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$params['testPointName']."'");
                 }
            }
            } if(isset($params['level']) && $params['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$params['level']."'");
            }
            if(isset($params['affiliation']) && $params['affiliation']!=''){
               $sQuery = $sQuery->where("spiv3.affiliation='".$params['affiliation']."'");
            }
            if(isset($params['province']) && is_array($params['province']) && count($params['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            if(is_array($params['district']) && count($params['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            }
            }else{
                if(isset($params['province']) && $params['province']!=''){
                    $provinces = explode(",",$params['province']);
                    $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                    ->where('f.province IN ("' . implode('", "', $provinces) . '")');
                }
            }
            if(isset($params['province']) && $params['province']!=''){
                if(is_array($params['district']) && count($params['district'])>0 ){
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $params['district']) . '")');
                }else{
                    if($params['district']!=''){
                        $provinces = explode(",",$params['district']);
                        $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                    }
                }
            }
            //if(isset($params['province']) && is_array($params['province']) && count($params['province'])>0 ){
            //    $sQuery = $sQuery->where('spiv3.level_name IN ("' . implode('", "', $params['province']) . '")');
            //}else{
            //    if(isset($params['province']) && $params['province']!=''){
            //        $provinces = explode(",",$params['province']);
            //        $sQuery = $sQuery->where('spiv3.level_name IN ("' . implode('", "', $provinces) . '")');
            //    }
            //}
            if(isset($params['scoreLevel']) && $params['scoreLevel']!=''){
                if($params['scoreLevel'] == 0){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
                }else if($params['scoreLevel'] == 1){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
                }else if($params['scoreLevel'] == 2){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
                }else if($params['scoreLevel'] == 3){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
                }else if($params['scoreLevel'] == 4){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
                }
            }
        
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
//        die($sQueryStr);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }
    
    
    public function getPerformanceLast30Days($params){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('-30 days', strtotime($start_date)));
        
        if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
            $dateField = explode(" ", $params['dateRange']);
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $start_date = $this->dateFormat($dateField[2]);                
            }
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $end_date = $this->dateFormat($dateField[0]);
            }
        }
        
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->columns(array(
                                                'newestDate' => new \Zend\Db\Sql\Expression("'$start_date'"),
                                                'oldestDate' => new \Zend\Db\Sql\Expression("'$end_date'"),
                                                'totalDataPoints' => new \Zend\Db\Sql\Expression("COUNT(*)"),                                    
                                                'level0' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) < 40, 1,0))"),
                                                'level1' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 40 and (AUDIT_SCORE_PERCANTAGE) < 60, 1,0))"),
                                                'level2' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 60 and (AUDIT_SCORE_PERCANTAGE) < 80, 1,0))"),
                                                'level3' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 80 and (AUDIT_SCORE_PERCANTAGE) < 90, 1,0))"),
                                                'level4' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 90, 1,0))"),
                                                ))
                                ->where("spiv3.status='approved'")
                                ->where("(`assesmentofaudit` BETWEEN '" . $start_date ."' - INTERVAL DATEDIFF('".$start_date."','".$end_date."') DAY AND '".$start_date."')");
        //    $sQuery = $sQuery->where("(`assesmentofaudit` BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE())");
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        if(isset($params['auditRndNo']) && $params['auditRndNo']!=''){
            $sQuery = $sQuery->where("spiv3.auditroundno='".$params['auditRndNo']."'");
        }
        if(isset($params['testPoint']) && trim($params['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$params['testPoint']."'");
            if(isset($params['testPointName']) && trim($params['testPointName'])!= ''){
                 if(trim($params['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$params['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$params['testPointName']."'");
                 }
            }
            } if(isset($params['level']) && $params['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$params['level']."'");
            }
            if(isset($params['affiliation']) && $params['affiliation']!=''){
               $sQuery = $sQuery->where("spiv3.affiliation='".$params['affiliation']."'");
            }
            if(isset($params['province']) && is_array($params['province']) && count($params['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            if(is_array($params['district']) && count($params['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            }
            }else{
                if(isset($params['province']) && $params['province']!=''){
                    $provinces = explode(",",$params['province']);
                    $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                    ->where('f.province IN ("' . implode('", "', $provinces) . '")');
                }
            }
            if(isset($params['province']) && $params['province']!=''){
                if(is_array($params['district']) && count($params['district'])>0 ){
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $params['district']) . '")');
                }else{
                    if($params['district']!=''){
                        $provinces = explode(",",$params['district']);
                        $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                    }
                }
            }
            if(isset($params['scoreLevel']) && $params['scoreLevel']!=''){
            if($params['scoreLevel'] == 0){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
            }else if($params['scoreLevel'] == 1){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
            }else if($params['scoreLevel'] == 2){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
            }else if($params['scoreLevel'] == 3){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
            }else if($params['scoreLevel'] == 4){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
            }
        }
        
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //die($sQueryStr);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        return $rResult;
    }
    
    public function getPerformanceLast180Days(){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $today = date('Y-m-d');
        $last180Date = date('Y-m-d', strtotime('-180 days', strtotime($today)));        
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->columns(array(
                                                'newestDate' => new \Zend\Db\Sql\Expression("'$today'"),
                                                'oldestDate' => new \Zend\Db\Sql\Expression("'$last180Date'"),
                                                'totalDataPoints' => new \Zend\Db\Sql\Expression("COUNT(*)"),
                                                'level0' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) < 40, 1,0))"),
                                                'level1' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 40 and (AUDIT_SCORE_PERCANTAGE) < 60, 1,0))"),
                                                'level2' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 60 and (AUDIT_SCORE_PERCANTAGE) < 80, 1,0))"),
                                                'level3' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 80 and (AUDIT_SCORE_PERCANTAGE) < 90, 1,0))"),
                                                'level4' => new \Zend\Db\Sql\Expression("SUM(IF((AUDIT_SCORE_PERCANTAGE) >= 90, 1,0))"),
                                                ))
                                ->where(array('status'=>'approved'))
                                ->where("(`assesmentofaudit` BETWEEN CURDATE() - INTERVAL 180 DAY AND CURDATE())");
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        return $rResult;
    }
    
    public function getAllSubmissions($sortOrder = 'DESC'){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where('spiv3.status != "deleted"')
                                ->order(array("status DESC","id $sortOrder"));
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }
    
    public function getAllApprovedTestingVolume($params){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->order(array("avgMonthTesting DESC"));
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $start_date = '';
        $end_date = '';
        if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
            $dateField = explode(" ", $params['dateRange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        
        if(isset($params['auditRndNo']) && $params['auditRndNo']!=''){
            $sQuery = $sQuery->where("spiv3.auditroundno='".$params['auditRndNo']."'");
        }
        if(isset($params['testPoint']) && trim($params['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$params['testPoint']."'");
            if(isset($params['testPointName']) && trim($params['testPointName'])!= ''){
                 if(trim($params['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$params['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$params['testPointName']."'");
                 }
            }
            } if(isset($params['level']) && $params['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$params['level']."'");
            }
            if(isset($params['affiliation']) && $params['affiliation']!=''){
               $sQuery = $sQuery->where("spiv3.affiliation='".$params['affiliation']."'");
            }
            if(isset($params['province']) && is_array($params['province']) && count($params['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            if(is_array($params['district']) && count($params['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            }
            }else{
                if(isset($params['province']) && $params['province']!=''){
                    $provinces = explode(",",$params['province']);
                    $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                    ->where('f.province IN ("' . implode('", "', $provinces) . '")');
                }
            }
            if(isset($params['province']) && $params['province']!=''){
                if(is_array($params['district']) && count($params['district'])>0 ){
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $params['district']) . '")');
                }else{
                    if($params['district']!=''){
                        $provinces = explode(",",$params['district']);
                        $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                    }
                }
            }
            if(isset($params['scoreLevel']) && $params['scoreLevel']!=''){
            if($params['scoreLevel'] == 0){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
            }else if($params['scoreLevel'] == 1){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
            }else if($params['scoreLevel'] == 2){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
            }else if($params['scoreLevel'] == 3){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
            }else if($params['scoreLevel'] == 4){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
            }
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }
    
    public function fetchAllSubmissionsDetails($parameters,$acl){
        $logincontainer = new Container('credo');
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	$queryContainer = new Container('query');
        $aColumns = array('id','facilityname','auditroundno',"DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')",'testingpointname' ,'testingpointtype','level','affiliation','AUDIT_SCORE_PERCANTAGE','status');
        $orderColumns = array('id','facilityname','auditroundno','assesmentofaudit','testingpointname' ,'testingpointtype','level','affiliation','AUDIT_SCORE_PERCANTAGE','status');

        /*
        * Paging
        */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
        * Ordering
        */

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
 
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
        * SQL queries
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $start_date = "";
        $end_date = "";
        if (isset($parameters['dateRange']) && ($parameters['dateRange'] != "")) {
            $dateField = explode(" ", $parameters['dateRange']);
            //print_r($proceed_date);die;
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where('spiv3.status != "deleted"');
        if($parameters['auditRndNo']!=''){
         $sQuery = $sQuery->where("spiv3.auditroundno='".$parameters['auditRndNo']."'");
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        if(isset($parameters['testPoint']) && trim($parameters['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$parameters['testPoint']."'");
            if(isset($parameters['testPointName']) && trim($parameters['testPointName'])!= ''){
                 if(trim($parameters['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$parameters['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$parameters['testPointName']."'");
                 }
            }
        }
        if($parameters['level']!=''){
         $sQuery = $sQuery->where("spiv3.level='".$parameters['level']."'");
        }
        if($parameters['affiliation']!=''){
         $sQuery = $sQuery->where("spiv3.affiliation='".$parameters['affiliation']."'");
        }
        if(is_array($parameters['province']) && count($parameters['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
            if(is_array($parameters['district']) && count($parameters['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
            }
        }else{
            if($parameters['province']!=''){
                $provinces = explode(",",$parameters['province']);
                $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                ->where('f.province IN ("' . implode('", "', $provinces) . '")');
            }
        }
        if($parameters['province']!=''){
            if(is_array($parameters['district']) && count($parameters['district'])>0 ){
                $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $parameters['district']) . '")');
            }else{
                if($parameters['district']!=''){
                    $provinces = explode(",",$parameters['district']);
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                }
            }
        }
        if(isset($parameters['scoreLevel']) && $parameters['scoreLevel']!=''){
            if($parameters['scoreLevel'] == 0){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
            }else if($parameters['scoreLevel'] == 1){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
            }else if($parameters['scoreLevel'] == 2){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
            }else if($parameters['scoreLevel'] == 3){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
            }else if($parameters['scoreLevel'] == 4){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
            }
        }
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }

        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }

        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }
        
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance
        $queryContainer->exportAllDataQuery = $sQuery;
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $tQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                 ->where('spiv3.status != "deleted"');
        if($parameters['auditRndNo']!=''){
          $tQuery = $tQuery->where("spiv3.auditroundno='".$parameters['auditRndNo']."'");
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $tQuery = $tQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        if(isset($parameters['testPoint']) && trim($parameters['testPoint'])!=''){
            $tQuery = $tQuery->where("spiv3.testingpointtype='".$parameters['testPoint']."'");
            if(isset($parameters['testPointName']) && trim($parameters['testPointName'])!= ''){
                 if(trim($parameters['testPoint'])!= 'other'){
                    $tQuery = $tQuery->where("spiv3.testingpointname='".$parameters['testPointName']."'");
                 }else{
                    $tQuery = $tQuery->where("spiv3.testingpointtype_other='".$parameters['testPointName']."'");
                 }
            }
        } if($parameters['level']!=''){
           $tQuery = $tQuery->where("spiv3.level='".$parameters['level']."'");
        }
        if($parameters['affiliation']!=''){
           $tQuery = $tQuery->where("spiv3.affiliation='".$parameters['affiliation']."'");
        }
        if(is_array($parameters['province']) && count($parameters['province'])>0 ){
            $tQuery = $tQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
            if(is_array($parameters['district']) && count($parameters['district'])>0 ){
                $tQuery = $tQuery->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
            }
        }else{
            if($parameters['province']!=''){
                $provinces = explode(",",$parameters['province']);
                $tQuery = $tQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                ->where('f.province IN ("' . implode('", "', $provinces) . '")');
            }
        }
        if($parameters['province']!=''){
            if(is_array($parameters['district']) && count($parameters['district'])>0 ){
                $tQuery = $tQuery->where('f.district IN ("' . implode('", "', $parameters['district']) . '")');
            }else{
                if($parameters['district']!=''){
                    $provinces = explode(",",$parameters['district']);
                    $tQuery = $tQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                }
            }
        }
        if(isset($parameters['scoreLevel']) && $parameters['scoreLevel']!=''){
            if($parameters['scoreLevel'] == 0){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
            }else if($parameters['scoreLevel'] == 1){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
            }else if($parameters['scoreLevel'] == 2){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
            }else if($parameters['scoreLevel'] == 3){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
            }else if($parameters['scoreLevel'] == 4){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
            }
        }
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $tQuery = $tQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
               "sEcho" => intval($parameters['sEcho']),
               "iTotalRecords" => $iTotal,
               "iTotalDisplayRecords" => $iFilteredTotal,
               "aaData" => array()
        );
        
        $role = $logincontainer->roleCode;
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'download-pdf')) {
            $downloadPdfAction = true;
        } else {
            $downloadPdfAction = false;
        }
        
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'approve-status')) {
            $approveStatusAction = true;
        } else {
            $approveStatusAction = false;
        }
        
        $commonService = new \Application\Service\CommonService();
        $auditScore = 0;
        $levelZero = array();
        $levelOne = array();
        $levelTwo = array();
        $levelThree = array();
        $levelFour = array();
        foreach ($rResult as $aRow) {
         $row = array();
         $approve = '';
         $downloadPdf="";
         $auditScore+=$aRow['AUDIT_SCORE_PERCANTAGE'];
         if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] < 40){
            $levelZero[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
         }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 40 && $aRow['AUDIT_SCORE_PERCANTAGE'] <60){
            $levelOne[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
         }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 60 && $aRow['AUDIT_SCORE_PERCANTAGE'] <80){
           $levelTwo[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
         }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 80 && $aRow['AUDIT_SCORE_PERCANTAGE'] <90){
           $levelThree[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
         }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 90){
           $levelFour[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
         }
         $row['DT_RowId'] = $aRow['id'];
         if(isset($aRow['level_other']) && $aRow['level_other'] != ""){
            $level = " - " .$aRow['level_other'];
         }else{
            $level = '';
         }
         $row[] = '';
         $row[] = $aRow['facilityname'];
         $row[] = $aRow['auditroundno'];
         $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
         $row[] = (isset($aRow['testingpointname']) && $aRow['testingpointname'] != "" ? $aRow['testingpointname'] : $aRow['testingpointtype']);
         $row[] = $aRow['testingpointtype'];
         $row[] = $aRow['level'].$level;
         $row[] = $aRow['affiliation'];
         $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
         $row[] = ucwords($aRow['status']);
         //$print = '<a href="/spi-v3/print/' . $aRow['id'] . '" target="_blank" style="white-space:nowrap;"><i class="fa fa-print"></i> Print</a>';
        if($aRow['status']=='pending'){
            if($approveStatusAction){
            $approve = '<br><a href="javascript:void(0);" onclick="approveStatus('.$aRow['id'].')"  style="white-space:nowrap;"><i class="fa fa-check"></i>  Approve</a>';
            }
        }
         
        if($downloadPdfAction){
            $downloadPdf = '<br><a href="javascript:void(0);" onclick="downloadPdf('.$aRow['id'].')" style="white-space:nowrap;"><i class="fa fa-download"></i> PDF</a>';
        }
         //$pending = '<br><a href="/spi-v3/edit/' . $aRow['id'] . '" style="white-space:nowrap;"><i class="fa fa-pencil"></i> Edit</a>';
         $row[] = $approve." ".$downloadPdf;
         $row[] = $aRow['PERSONAL_SCORE'];
         $row[] = $aRow['PHYSICAL_SCORE'];
         $row[] = $aRow['SAFETY_SCORE'];
         $row[] = $aRow['PRETEST_SCORE'];
         $row[] = $aRow['TEST_SCORE'];
         $row[] = $aRow['POST_SCORE'];
         $row[] = $aRow['EQA_SCORE'];
         $t = $aRow['PERSONAL_SCORE'] + $aRow['PHYSICAL_SCORE'] +$aRow['SAFETY_SCORE'] + $aRow['PRETEST_SCORE'] + $aRow['TEST_SCORE'] + $aRow['POST_SCORE'] + $aRow['EQA_SCORE'];
         $row[] = $t."(".round($aRow['AUDIT_SCORE_PERCANTAGE'],2).")";
         $output['aaData'][] = $row;
        }
        //get earliest date
        $eQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->columns(array('assesmentofaudit'))->order('assesmentofaudit ASC');
        $eQueryStr = $sql->getSqlStringForSqlObject($eQuery); // Get the string of the Sql, instead of the Select-instance
        $eResult = $dbAdapter->query($eQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        //get duplicate value
        $dpResult = $dbAdapter->query("SELECT `meta-instance-id`, COUNT(*) c FROM spi_form_v_3 GROUP BY `meta-instance-id` HAVING c > 1", $dbAdapter::QUERY_MODE_EXECUTE)->toArray();;
        
        $output['avgAuditScore'] = (count($rResult) > 0) ? round($auditScore/count($rResult),2) : 0;
        $output['levelZeroCount'] = count($levelZero);
        $output['levelOneCount'] = count($levelOne);
        $output['levelTwoCount'] = count($levelTwo);
        $output['levelThreeCount'] = count($levelThree);
        $output['levelFourCount'] = count($levelFour);
        $output['eDate'] = $eResult['assesmentofaudit'];
        $output['duplicate'] = count($dpResult);
        return $output;
    }
    
    public function fetchAllDuplicateSubmissionsDetails(){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $rResult = $dbAdapter->query("SELECT `meta-instance-id`,`id`,`facilityname`,`status`,`auditroundno`,`AUDIT_SCORE_PERCANTAGE`,`affiliation`,`level`,`assesmentofaudit`,`testingpointtype`,`testingpointname`, COUNT(*) c FROM spi_form_v_3 GROUP BY `meta-instance-id` HAVING c > 1", $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }
    
    
    public function fetchAllSubmissionsDatas($parameters,$acl){
        $logincontainer = new Container('credo');
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	
        $aColumns = array('facilityname','auditroundno','assesmentofaudit' ,'testingpointtype','level','affiliation','AUDIT_SCORE_PERCANTAGE','status');
        $orderColumns = array('facilityname','auditroundno','assesmentofaudit' ,'testingpointtype','level','affiliation','AUDIT_SCORE_PERCANTAGE','status');

        /*
        * Paging
        */
       $sLimit = "";
       if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
           $sOffset = $parameters['iDisplayStart'];
           $sLimit = $parameters['iDisplayLength'];
       }

        /*
        * Ordering
        */

       $sOrder = "";
       if (isset($parameters['iSortCol_0'])) {
           for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
               if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                   $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
               }
           }
           $sOrder = substr_replace($sOrder, "", -1);
       }

       /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

       $sWhere = "";
       if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
           $searchArray = explode(" ", $parameters['sSearch']);
           $sWhereSub = "";
           foreach ($searchArray as $search) {
               if ($sWhereSub == "") {
                   $sWhereSub .= "(";
               } else {
                   $sWhereSub .= " AND (";
               }
               $colSize = count($aColumns);

               for ($i = 0; $i < $colSize; $i++) {
                   if ($i < $colSize - 1) {
                       $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                   } else {
                       $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                   }
               }
               $sWhereSub .= ")";
           }
           $sWhere .= $sWhereSub;
       }

       /* Individual column filtering */
       for ($i = 0; $i < count($aColumns); $i++) {
           if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
               if ($sWhere == "") {
                   $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
               } else {
                   $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
               }
           }
       }

       /*
        * SQL queries
        * Get data to display
        */
       $dbAdapter = $this->adapter;
       $sql = new Sql($dbAdapter);
       $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                               ->where('spiv3.status != "deleted"');
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
       if (isset($sWhere) && $sWhere != "") {
           $sQuery->where($sWhere);
       }

       if (isset($sOrder) && $sOrder != "") {
           $sQuery->order($sOrder);
       }

       if (isset($sLimit) && isset($sOffset)) {
           $sQuery->limit($sLimit);
           $sQuery->offset($sOffset);
       }

       $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance 
       //echo $sQueryStr;die;
       $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

       /* Data set length after filtering */
       $sQuery->reset('limit');
       $sQuery->reset('offset');
       $fQuery = $sql->getSqlStringForSqlObject($sQuery);
       $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
       $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $tQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                 ->where('spiv3.status != "deleted"');
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $tQuery = $tQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
               "sEcho" => intval($parameters['sEcho']),
               "iTotalRecords" => $iTotal,
               "iTotalDisplayRecords" => $iFilteredTotal,
               "aaData" => array()
        );
        $role = $logincontainer->roleCode;
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'delete')) {
            $delete = true;
        } else {
            $delete = false;
        }
        
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'download-pdf')) {
            $downloadPdfAction = true;
        } else {
            $downloadPdfAction = false;
        }
        
        $commonService = new \Application\Service\CommonService();
       foreach ($rResult as $aRow) {
        $row = array();
        $downloadPdf="";
        $edit="";
        $remove="";
        $row['DT_RowId'] = $aRow['id'];
        $row[] = $aRow['facilityname'];
        $row[] = $aRow['auditroundno'];
        $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
        $row[] = $aRow['testingpointtype'];
        $row[] = $aRow['level'];
        $row[] = $aRow['affiliation'];
        $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
        $row[] = ucwords($aRow['status']);
        if($downloadPdfAction){
            //$downloadPdf = '<a href="javascript:void(0);" onclick="downloadPdf('.$aRow['id'].')" style="white-space:nowrap;"><i class="fa fa-download"></i> PDF</a>';
        }
        if($update){
            $edit = '&nbsp;<a href="/spi-v3/edit/' . $aRow['id'] . '" style="white-space:nowrap;"><i class="fa fa-pencil"></i> Edit</a>';
        }
        if($delete){
            $remove = '&nbsp;<a href="javascript:void(0);" onclick="deleteAudit('.$aRow['id'].');" style="white-space:nowrap;"><i class="fa fa-times"></i> Delete</a>';
        }
        $row[] = $edit." ".$downloadPdf. " ".$remove;
        $output['aaData'][] = $row;
       }
       return $output;
    }
    
    public function fetchPendingFacilityNames()
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('spiv3.status'=>'pending'));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }

    public function getFormData($id) {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('spiv3.id'=>$id));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $sResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        if($sResult){
            if(trim($sResult->facility)!= '' || trim($sResult->facilityid)!= '' || trim($sResult->facilityname)!= ''){
                $fQuery = $sql->select()->from(array('spirt3' => 'spi_rt_3_facilities'))
                                        ->columns(array('fId'=>'id','ffId'=>'facility_id','fName'=>'facility_name','fEmail'=>'email','fCPerson'=>'contact_person','fDistrict'=>'district','fProvince'=>'province','fLatitude'=>'latitude','fLongitude'=>'longitude'));
                if(isset($sResult->facility) && $sResult->facility >0){
                   $fQuery = $fQuery->where("spirt3.id='".$sResult->facility."'");
                }else if(isset($sResult->facilityid) && $sResult->facilityid!= ''){
                   $fQuery = $fQuery->where("spirt3.facility_id='".$sResult->facilityid."'");
                }else if(isset($sResult->facilityname) && $sResult->facilityname!= ''){
                   $fQuery = $fQuery->where("spirt3.facility_name='".$sResult->facilityname."'");
                }
                $fQueryStr = $sql->getSqlStringForSqlObject($fQuery);
                $sResult['facilityInfo'] = $dbAdapter->query($fQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            }
        }
      return $sResult;
    }

    public function getAuditRoundWiseData($params) {
        $logincontainer = new Container('credo');
        //$rResult = $this->getAllSubmissions();
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->order(array("id DESC"));
        if(isset($params['roundno']) && $params['roundno']!=''){
            $sQuery = $sQuery->where('spiv3.auditroundno IN ("' . implode('", "', $params['roundno']) . '")');
        }
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $start_date = '';
        $end_date = '';
        if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
            $dateField = explode(" ", $params['dateRange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        if($params['auditRndNo']!=''){
            $sQuery = $sQuery->where("spiv3.auditroundno='".$params['auditRndNo']."'");
        }
        
        if(isset($params['testPoint']) && trim($params['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$params['testPoint']."'");
            if(isset($params['testPointName']) && trim($params['testPointName'])!= ''){
                 if(trim($params['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$params['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$params['testPointName']."'");
                 }
            }
            } if(isset($params['level']) && $params['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$params['level']."'");
            }
            if(isset($params['affiliation']) && $params['affiliation']!=''){
               $sQuery = $sQuery->where("spiv3.affiliation='".$params['affiliation']."'");
            }
            if(is_array($params['province']) && count($params['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            if(is_array($params['district']) && count($params['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            }
            }else{
                if($params['province']!=''){
                    $provinces = explode(",",$params['province']);
                    $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                    ->where('f.province IN ("' . implode('", "', $provinces) . '")');
                }
            }
            if($params['province']!=''){
                if(is_array($params['district']) && count($params['district'])>0 ){
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $params['district']) . '")');
                }else{
                    if($params['district']!=''){
                        $provinces = explode(",",$params['district']);
                        $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                    }
                }
            }
            //if(isset($params['province']) && is_array($params['province']) && count($params['province'])>0 ){
            //    $sQuery = $sQuery->where('spiv3.level_name IN ("' . implode('", "', $params['province']) . '")');
            //}else{
            //    if(isset($params['province']) && $params['province']!=''){
            //        $provinces = explode(",",$params['province']);
            //        $sQuery = $sQuery->where('spiv3.level_name IN ("' . implode('", "', $provinces) . '")');
            //    }
            //}
            if(isset($params['scoreLevel']) && $params['scoreLevel']!=''){
                if($params['scoreLevel'] == 0){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
                }else if($params['scoreLevel'] == 1){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
                }else if($params['scoreLevel'] == 2){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
                }else if($params['scoreLevel'] == 3){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
                }else if($params['scoreLevel'] == 4){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
                }
            }
        
        
        if(isset($params['fieldName']) && trim($params['fieldName'])!=''){
            $sQuery = $sQuery->where(array($params['fieldName']=>$params['val']));
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        
        $response = array();
        
        foreach($rResult as $row){
            //$response[$row['auditroundno']]['PERSONAL_SCORE'][]=  $row['PERSONAL_SCORE'];
            //$response[$row['auditroundno']]['PHYSICAL_SCORE'][]=  $row['PHYSICAL_SCORE'];
            //$response[$row['auditroundno']]['SAFETY_SCORE'][]=  $row['SAFETY_SCORE'];
            //$response[$row['auditroundno']]['PRETEST_SCORE'][]=  $row['PRETEST_SCORE'];
            //$response[$row['auditroundno']]['TEST_SCORE'][]=  $row['TEST_SCORE'];
            //$response[$row['auditroundno']]['POST_SCORE'][]=  $row['POST_SCORE'];
            //$response[$row['auditroundno']]['EQA_SCORE'][]=  $row['EQA_SCORE'];
            $response[0]['PERSONAL_SCORE'][]=  $row['PERSONAL_SCORE'];
            $response[0]['PHYSICAL_SCORE'][]=  $row['PHYSICAL_SCORE'];
            $response[0]['SAFETY_SCORE'][]=  $row['SAFETY_SCORE'];
            $response[0]['PRETEST_SCORE'][]=  $row['PRETEST_SCORE'];
            $response[0]['TEST_SCORE'][]=  $row['TEST_SCORE'];
            $response[0]['POST_SCORE'][]=  $row['POST_SCORE'];
            $response[0]['EQA_SCORE'][]=  $row['EQA_SCORE'];
        }
        
        $auditRoundWiseData = array();
        foreach($response as $auditNo => $auditScores){
            $auditRoundWiseData[$auditNo]['PERSONAL_SCORE'] = array_sum($auditScores['PERSONAL_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            $auditRoundWiseData[$auditNo]['PHYSICAL_SCORE'] = array_sum($auditScores['PHYSICAL_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            $auditRoundWiseData[$auditNo]['SAFETY_SCORE'] = array_sum($auditScores['SAFETY_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            $auditRoundWiseData[$auditNo]['PRETEST_SCORE'] = array_sum($auditScores['PRETEST_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            $auditRoundWiseData[$auditNo]['TEST_SCORE'] = array_sum($auditScores['TEST_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            $auditRoundWiseData[$auditNo]['POST_SCORE'] = array_sum($auditScores['POST_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            $auditRoundWiseData[$auditNo]['EQA_SCORE'] = array_sum($auditScores['EQA_SCORE']) / count($auditScores['PERSONAL_SCORE']);
            
        }
        $response = array('');
        return $auditRoundWiseData;
     
    }
    

    public function getZeroQuestionCounts($params) {
        $logincontainer = new Container('credo');
        //$rResult = $this->fetchAllApprovedSubmissions();
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('spiv3.status'=>'approved'))
                                ->order(array("assesmentofaudit DESC"));
        if(isset($params['roundno']) && $params['roundno']!=''){
            $sQuery = $sQuery->where('spiv3.auditroundno IN ("' . implode('", "', $params['roundno']) . '")');
        }
        
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $start_date = '';
        $end_date = '';
        if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
            $dateField = explode(" ", $params['dateRange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        if($params['auditRndNo']!=''){
            $sQuery = $sQuery->where("spiv3.auditroundno='".$params['auditRndNo']."'");
        }
        
        if(isset($params['testPoint']) && trim($params['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$params['testPoint']."'");
            if(isset($params['testPointName']) && trim($params['testPointName'])!= ''){
                 if(trim($params['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$params['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$params['testPointName']."'");
                 }
            }
            } if(isset($params['level']) && $params['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$params['level']."'");
            }
            if(isset($params['affiliation']) && $params['affiliation']!=''){
               $sQuery = $sQuery->where("spiv3.affiliation='".$params['affiliation']."'");
            }
            if(is_array($params['province']) && count($params['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            if(is_array($params['district']) && count($params['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            }
            }else{
                if($params['province']!=''){
                    $provinces = explode(",",$params['province']);
                    $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                    ->where('f.province IN ("' . implode('", "', $provinces) . '")');
                }
            }
            if($params['province']!=''){
                if(is_array($params['district']) && count($params['district'])>0 ){
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $params['district']) . '")');
                }else{
                    if($params['district']!=''){
                        $provinces = explode(",",$params['district']);
                        $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                    }
                }
            }
            if(isset($params['scoreLevel']) && $params['scoreLevel']!=''){
                if($params['scoreLevel'] == 0){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
                }else if($params['scoreLevel'] == 1){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
                }else if($params['scoreLevel'] == 2){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
                }else if($params['scoreLevel'] == 3){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
                }else if($params['scoreLevel'] == 4){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
                }
            }
        
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        $response = array();
        
        $questionColums = array(
                                'PERSONAL_Q_1_1',
                                'PERSONAL_Q_1_2',
                                'PERSONAL_Q_1_3',
                                'PERSONAL_Q_1_4',
                                'PERSONAL_Q_1_5',
                                'PERSONAL_Q_1_6',
                                'PERSONAL_Q_1_7',
                                'PERSONAL_Q_1_8',
                                'PERSONAL_Q_1_9',
                                'PERSONAL_Q_1_10',
                                'PHYSICAL_Q_2_1',
                                'PHYSICAL_Q_2_2',
                                'PHYSICAL_Q_2_3',
                                'PHYSICAL_Q_2_4',
                                'PHYSICAL_Q_2_5',
                                'SAFETY_Q_3_1',
                                'SAFETY_Q_3_2',
                                'SAFETY_Q_3_3',
                                'SAFETY_Q_3_4',
                                'SAFETY_Q_3_5',
                                'SAFETY_Q_3_6',
                                'SAFETY_Q_3_7',
                                'SAFETY_Q_3_8',
                                'SAFETY_Q_3_9',
                                'SAFETY_Q_3_10',
                                'SAFETY_Q_3_11',
                                'PRE_Q_4_1',
                                'PRE_Q_4_2',
                                'PRE_Q_4_3',
                                'PRE_Q_4_4',
                                'PRE_Q_4_5',
                                'PRE_Q_4_6',
                                'PRE_Q_4_7',
                                'PRE_Q_4_8',
                                'PRE_Q_4_9',
                                'PRE_Q_4_10',
                                'PRE_Q_4_11',
                                'PRE_Q_4_12',
                                'TEST_Q_5_1',
                                'TEST_Q_5_2',
                                'TEST_Q_5_3',
                                'TEST_Q_5_4',
                                'TEST_Q_5_5',
                                'TEST_Q_5_6',
                                'TEST_Q_5_7',
                                'TEST_Q_5_8',
                                'TEST_Q_5_9',
                                'POST_Q_6_1',
                                'POST_Q_6_2',
                                'POST_Q_6_3',
                                'POST_Q_6_4',
                                'POST_Q_6_5',
                                'POST_Q_6_6',
                                'POST_Q_6_7',
                                'POST_Q_6_8',
                                'POST_Q_6_9',
                                'EQA_Q_7_1',
                                'EQA_Q_7_2',
                                'EQA_Q_7_3',
                                'EQA_Q_7_4',
                                'EQA_Q_7_5',
                                'EQA_Q_7_6',
                                'EQA_Q_7_7',
                                'EQA_Q_7_8',
                                'EQA_Q_7_9',
                                'EQA_Q_7_10',
                                'EQA_Q_7_11',
                                'EQA_Q_7_12',
                                'EQA_Q_7_13',
                                'EQA_Q_7_14'
                                );
        
        
        
        foreach($rResult as $row){
            foreach($row as $col => $val){
                if(in_array($col,$questionColums)){
                    if($val == "0"){
                        if(isset($response[$col])){
                            $response[$col] = $response[$col] + 1;   
                        }else{
                            $response[$col] = 1;    
                        }
                        
                    }
                }
            }
        }
        arsort($response);
        return $response;
    }
    
    public function updateFormStatus($id,$status){
        if (trim($id) != "" && trim($status) != '') {
            $data = array('status' => $status);
            $this->update($data, array('id' => $id));
        }
        return $id;
    }
    
    public function fetchAllApprovedSubmissions($sortOrder = 'DESC'){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('status'=>'approved'))
                                ->order(array("assesmentofaudit $sortOrder"));
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }
    
    public function fecthAllApprovedSubmissionsTable($parameters){
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	$queryContainer = new Container('query');
        $logincontainer = new Container('credo');
        $aColumns = array('facilityname','assesmentofaudit' ,'testingpointname','PERSONAL_SCORE','PHYSICAL_SCORE','SAFETY_SCORE','PRETEST_SCORE','TEST_SCORE','POST_SCORE','EQA_SCORE','FINAL_AUDIT_SCORE','AUDIT_SCORE_PERCANTAGE');
        $orderColumns = array('facilityname','assesmentofaudit' ,'testingpointname','PERSONAL_SCORE','PHYSICAL_SCORE','SAFETY_SCORE','PRETEST_SCORE','TEST_SCORE','POST_SCORE','EQA_SCORE','FINAL_AUDIT_SCORE','AUDIT_SCORE_PERCANTAGE');

        /*
        * Paging
        */
       $sLimit = "";
       if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
           $sOffset = $parameters['iDisplayStart'];
           $sLimit = $parameters['iDisplayLength'];
       }

        /*
        * Ordering
        */

       $sOrder = "";
       if (isset($parameters['iSortCol_0'])) {
           for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
               if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                   $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
               }
           }
           $sOrder = substr_replace($sOrder, "", -1);
       }

       /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

       $sWhere = "";
       if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
           $searchArray = explode(" ", $parameters['sSearch']);
           $sWhereSub = "";
           foreach ($searchArray as $search) {
               if ($sWhereSub == "") {
                   $sWhereSub .= "(";
               } else {
                   $sWhereSub .= " AND (";
               }
               $colSize = count($aColumns);

               for ($i = 0; $i < $colSize; $i++) {
                   if ($i < $colSize - 1) {
                       $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                   } else {
                       $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                   }
               }
               $sWhereSub .= ")";
           }
           $sWhere .= $sWhereSub;
       }

       /* Individual column filtering */
       for ($i = 0; $i < count($aColumns); $i++) {
           if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
               if ($sWhere == "") {
                   $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
               } else {
                   $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
               }
           }
       }

       /*
        * SQL queries
        * Get data to display
        */
       $dbAdapter = $this->adapter;
       $sql = new Sql($dbAdapter);
        $start_date = '';
        $end_date = '';
        if (isset($parameters['dateRange']) && ($parameters['dateRange'] != "")) {
            $dateField = explode(" ", $parameters['dateRange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('spiv3.status'=>'approved'));
        if($parameters['auditRndNo']!=''){
         $sQuery = $sQuery->where("spiv3.auditroundno='".$parameters['auditRndNo']."'");
        }if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }if(isset($parameters['testPoint']) && trim($parameters['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$parameters['testPoint']."'");
            if(isset($parameters['testPointName']) && trim($parameters['testPointName'])!= ''){
                 if(trim($parameters['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$parameters['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$parameters['testPointName']."'");
                 }
            }
        }if($parameters['level']!=''){
         $sQuery = $sQuery->where("spiv3.level='".$parameters['level']."'");
        }
        if(is_array($parameters['province']) && count($parameters['province'])>0 ){
        $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                        ->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
        if(is_array($parameters['district']) && count($parameters['district'])>0 ){
            $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
        }
        }else{
            if($parameters['province']!=''){
                $provinces = explode(",",$parameters['province']);
                $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                ->where('f.province IN ("' . implode('", "', $provinces) . '")');
            }
        }
        if($parameters['province']!=''){
            if(is_array($parameters['district']) && count($parameters['district'])>0 ){
                $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $parameters['district']) . '")');
            }else{
                if($parameters['district']!=''){
                    $provinces = explode(",",$parameters['district']);
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                }
            }
        }
        if($parameters['affiliation']!=''){
         $sQuery = $sQuery->where("spiv3.affiliation='".$parameters['affiliation']."'");
        }if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }if(isset($parameters['scoreLevel']) && $parameters['scoreLevel']!=''){
            if($parameters['scoreLevel'] == 0){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
            }else if($parameters['scoreLevel'] == 1){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
            }else if($parameters['scoreLevel'] == 2){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
            }else if($parameters['scoreLevel'] == 3){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
            }else if($parameters['scoreLevel'] == 4){
              $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
            }
        }
       if (isset($sWhere) && $sWhere != "") {
           $sQuery->where($sWhere);
       }

       if (isset($sOrder) && $sOrder != "") {
           $sQuery->order($sOrder);
       }

       if (isset($sLimit) && isset($sOffset)) {
           $sQuery->limit($sLimit);
           $sQuery->offset($sOffset);
       }
       $queryContainer->exportQuery = $sQuery;
       $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance 
       //echo $sQueryStr;die;
       $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

       /* Data set length after filtering */
       $sQuery->reset('limit');
       $sQuery->reset('offset');
       $fQuery = $sql->getSqlStringForSqlObject($sQuery);
       $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
       $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $tQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('spiv3.status'=>'approved'));
        if($parameters['auditRndNo']!=''){
           $tQuery = $tQuery->where("spiv3.auditroundno='".$parameters['auditRndNo']."'");
        }if (trim($start_date) != "" && trim($end_date) != "") {
            $tQuery = $tQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }if(isset($parameters['testPoint']) && trim($parameters['testPoint'])!=''){
            $tQuery = $tQuery->where("spiv3.testingpointtype='".$parameters['testPoint']."'");
            if(isset($parameters['testPointName']) && trim($parameters['testPointName'])!= ''){
                 if(trim($parameters['testPoint'])!= 'other'){
                    $tQuery = $tQuery->where("spiv3.testingpointname='".$parameters['testPointName']."'");
                 }else{
                    $tQuery = $tQuery->where("spiv3.testingpointtype_other='".$parameters['testPointName']."'");
                 }
            }
        }if($parameters['level']!=''){
         $tQuery = $tQuery->where("spiv3.level='".$parameters['level']."'");
        }if(is_array($parameters['province']) && count($parameters['province'])>0 ){
        $tQuery = $tQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                        ->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
        if(is_array($parameters['district']) && count($parameters['district'])>0 ){
            $tQuery = $tQuery->where('f.province IN ("' . implode('", "', $parameters['province']) . '")');
        }
        }else{
            if($parameters['province']!=''){
                $provinces = explode(",",$parameters['province']);
                $tQuery = $tQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                ->where('f.province IN ("' . implode('", "', $provinces) . '")');
            }
        }
        if($parameters['province']!=''){
            if(is_array($parameters['district']) && count($parameters['district'])>0 ){
                $tQuery = $tQuery->where('f.district IN ("' . implode('", "', $parameters['district']) . '")');
            }else{
                if($parameters['district']!=''){
                    $provinces = explode(",",$parameters['district']);
                    $tQuery = $tQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                }
            }
        }if($parameters['affiliation']!=''){
         $tQuery = $tQuery->where("spiv3.affiliation='".$parameters['affiliation']."'");
        }if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $tQuery = $tQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }if(isset($parameters['scoreLevel']) && $parameters['scoreLevel']!=''){
            if($parameters['scoreLevel'] == 0){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
            }else if($parameters['scoreLevel'] == 1){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
            }else if($parameters['scoreLevel'] == 2){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
            }else if($parameters['scoreLevel'] == 3){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
            }else if($parameters['scoreLevel'] == 4){
              $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
            }
        }
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
               "sEcho" => intval($parameters['sEcho']),
               "iTotalRecords" => $iTotal,
               "iTotalDisplayRecords" => $iFilteredTotal,
               "aaData" => array()
        );
        
        $commonService = new \Application\Service\CommonService();
       foreach ($rResult as $aRow) {
        $row = array();
        $row[] = $aRow['facilityname'];
        $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
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
        $output['aaData'][] = $row;
       }
       return $output;
    }
    
    public function fetchAllApprovedSubmissionLocation($params){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where(array('spiv3.status'=>'approved'))
                                ->order(array("assesmentofaudit DESC"));
        if(isset($params['roundno']) && $params['roundno']!=''){
            $sQuery = $sQuery->where('spiv3.auditroundno IN ("' . implode('", "', $params['roundno']) . '")');
        }
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $start_date = '';
        $end_date = '';
        if (isset($params['dateRange']) && ($params['dateRange'] != "")) {
            $dateField = explode(" ", $params['dateRange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        if($params['auditRndNo']!=''){
            $sQuery = $sQuery->where("spiv3.auditroundno='".$params['auditRndNo']."'");
        }
        
        if(isset($params['testPoint']) && trim($params['testPoint'])!=''){
            $sQuery = $sQuery->where("spiv3.testingpointtype='".$params['testPoint']."'");
            if(isset($params['testPointName']) && trim($params['testPointName'])!= ''){
                 if(trim($params['testPoint'])!= 'other'){
                    $sQuery = $sQuery->where("spiv3.testingpointname='".$params['testPointName']."'");
                 }else{
                    $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$params['testPointName']."'");
                 }
            }
            } if(isset($params['level']) && $params['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$params['level']."'");
            }
            if(isset($params['affiliation']) && $params['affiliation']!=''){
               $sQuery = $sQuery->where("spiv3.affiliation='".$params['affiliation']."'");
            }
            if(is_array($params['province']) && count($params['province'])>0 ){
            $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                            ->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            if(is_array($params['district']) && count($params['district'])>0 ){
                $sQuery = $sQuery->where('f.province IN ("' . implode('", "', $params['province']) . '")');
            }
            }else{
                if($params['province']!=''){
                    $provinces = explode(",",$params['province']);
                    $sQuery = $sQuery->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                                    ->where('f.province IN ("' . implode('", "', $provinces) . '")');
                }
            }
            if($params['province']!=''){
                if(is_array($params['district']) && count($params['district'])>0 ){
                    $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $params['district']) . '")');
                }else{
                    if($params['district']!=''){
                        $provinces = explode(",",$params['district']);
                        $sQuery = $sQuery->where('f.district IN ("' . implode('", "', $provinces) . '")');
                    }
                }
            }
            if(isset($params['scoreLevel']) && $params['scoreLevel']!=''){
                if($params['scoreLevel'] == 0){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
                }else if($params['scoreLevel'] == 1){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
                }else if($params['scoreLevel'] == 2){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
                }else if($params['scoreLevel'] == 3){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
                }else if($params['scoreLevel'] == 4){
                  $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
                }
            }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        return $rResult;
    }
    
    public function dateFormat($date) {
        if (!isset($date) || $date == null || $date == "" || $date == "0000-00-00") {
            return "0000-00-00";
        } else {
            $dateArray = explode('-', $date);
            if (sizeof($dateArray) == 0) {
                return;
            }
            $newDate = $dateArray[2] . "-";

            $monthsArray = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $mon = 1;
            $mon += array_search(ucfirst($dateArray[1]), $monthsArray);

            if (strlen($mon) == 1) {
                $mon = "0" . $mon;
            }
            return $newDate .= $mon . "-" . $dateArray[0];
        }
    }
    
    public function updateSpiFormDetails($params){
        //\Zend\Debug\Debug::dump($params);die;
        if (trim($params['formId']) != "") {
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);
            $formId=base64_decode($params['formId']);
            $summationData="";
            if(isset($params['sectionNo'])){
                $n=count($params['sectionNo']);
                for ($i = 0; $i < $n; $i++) {
					$rowId=$params['rowId'][$i];
					if(isset($params['sectionNo'][$i]) && trim($params['sectionNo'][$i])!="" && trim($params['deficiency'][$i])!="" && trim($params['correction'.$rowId])!=""){
						$summationData[] = array(
						'sectionno' => $params['sectionNo'][$i],
						'deficiency' => $params['deficiency'][$i],
						'correction' => $params['correction'.$rowId],
						'auditorcomment' => $params['auditorComment'][$i],
						'action' => $params['action'][$i],
						'timeline' => $params['timeline'][$i],
						);
					}
                }
                $summationData=json_encode($summationData,true);
            }
            if($params['province']=='other' && trim($params['provinceTextBox'])!='')
            {
               $params['province'] =  $params['provinceTextBox'];
            }
            if($params['district']=='other' && trim($params['districtTextBox'])!='')
            {
               $params['district'] =  $params['districtTextBox'];
            }
            
            //update facility tbl
            $id = 0;
            $facilityDb = new SpiRtFacilitiesTable($dbAdapter);
            if(trim($params['testingFacility'])!= ''){
                $id = base64_decode($params['testingFacility']);
                $facilityDb->updateFacilityInfo($id,$params);
            }else if(trim($params['testingFacilityName'])!= ''){
                $fQuery = $sql->select()->from(array('spirt3' => 'spi_rt_3_facilities'))
                                        ->columns(array('id'))
                                        ->where("spirt3.facility_name='".$params['testingFacilityName']."'");
                $fQueryStr = $sql->getSqlStringForSqlObject($fQuery);
                $fResult = $dbAdapter->query($fQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
                if($fResult){
                   $id = $fResult->id;
                   $facilityDb->updateFacilityInfo($id,$params);
                }else{
                   $id = $facilityDb->addFacilityInfo($params); 
                }
            }
            
            $data = array(
                //'assesmentofaudit' => $this->dateFormat($params['auditDate']),
                'auditroundno' => $params['auditRound'],
                'facility' => ($id > 0)?$id:null,
                'facilityid' => $params['testingFacilityId'],
                'facilityname' => $params['testingFacilityName'],
                'testingpointname' => $params['testingPointName'],
                'testingpointtype' => $params['testingPointType'],
                'testingpointtype_other' => $params['testingPointTypeOther'],
                'locationaddress' => $params['location'],
                'level' => $params['level'],
                'level_other' => $params['levelOther'],
                'level_name' => $params['levelName'],
                'affiliation' => $params['affiliation'],
                'affiliation_other' => $params['affiliationOther'],
                'NumberofTester' => (isset($params['NumberofTester']) && $params['NumberofTester'] > 0 ? $params['NumberofTester'] : 0),
                'avgMonthTesting' => (isset($params['avgMonthTesting']) && $params['avgMonthTesting'] > 0 ? $params['avgMonthTesting'] : 0),
                'name_auditor_lead' => $params['name_auditor_lead'],
                'name_auditor2' => $params['name_auditor2'],
                'PERSONAL_C_1_1' => $params['personal_c_1_1'],
                'PERSONAL_C_1_2' => $params['personal_c_1_2'],
                'PERSONAL_C_1_3' => $params['personal_c_1_3'],
                'PERSONAL_C_1_4' => $params['personal_c_1_4'],
                'PERSONAL_C_1_5' => $params['personal_c_1_5'],
                'PERSONAL_C_1_6' => $params['personal_c_1_6'],
                'PERSONAL_C_1_7' => $params['personal_c_1_7'],
                'PERSONAL_C_1_8' => $params['personal_c_1_8'],
                'PERSONAL_C_1_9' => $params['personal_c_1_9'],
                'PERSONAL_C_1_10' => $params['personal_c_1_10'],
                'PHYSICAL_C_2_1' => $params['physical_c_2_1'],
                'PHYSICAL_C_2_2' => $params['physical_c_2_2'],
                'PHYSICAL_C_2_3' => $params['physical_c_2_3'],
                'PHYSICAL_C_2_4' => $params['physical_c_2_4'],
                'PHYSICAL_C_2_5' => $params['physical_c_2_5'],
                'SAFETY_C_3_1' => $params['safety_c_3_1'],
                'SAFETY_C_3_2' => $params['safety_c_3_2'],
                'SAFETY_C_3_3' => $params['safety_c_3_3'],
                'SAFETY_C_3_4' => $params['safety_c_3_4'],
                'SAFETY_C_3_5' => $params['safety_c_3_5'],
                'SAFETY_C_3_6' => $params['safety_c_3_6'],
                'SAFETY_C_3_7' => $params['safety_c_3_7'],
                'SAFETY_C_3_8' => $params['safety_c_3_8'],
                'SAFETY_C_3_9' => $params['safety_c_3_9'],
                'SAFETY_C_3_10' => $params['safety_c_3_10'],
                'SAFETY_C_3_11' => $params['safety_c_3_11'],
                'PRE_C_4_1' => $params['pre_c_4_1'],
                'PRE_C_4_2' => $params['pre_c_4_2'],
                'PRE_C_4_3' => $params['pre_c_4_3'],
                'PRE_C_4_4' => $params['pre_c_4_4'],
                'PRE_C_4_5' => $params['pre_c_4_5'],
                'PRE_C_4_6' => $params['pre_c_4_6'],
                'PRE_C_4_7' => $params['pre_c_4_7'],
                'PRE_C_4_8' => $params['pre_c_4_8'],
                'PRE_C_4_9' => $params['pre_c_4_9'],
                'PRE_C_4_10' => $params['pre_c_4_10'],
                'PRE_C_4_11' => $params['pre_c_4_11'],
                'PRE_C_4_12' => $params['pre_c_4_12'],
                'TEST_C_5_1' => $params['test_c_5_1'],
                'TEST_C_5_2' => $params['test_c_5_2'],
                'TEST_C_5_3' => $params['test_c_5_3'],
                'TEST_C_5_4' => $params['test_c_5_4'],
                'TEST_C_5_5' => $params['test_c_5_5'],
                'TEST_C_5_6' => $params['test_c_5_6'],
                'TEST_C_5_7' => $params['test_c_5_7'],
                'TEST_C_5_8' => $params['test_c_5_8'],
                'TEST_C_5_9' => $params['test_c_5_9'],
                'POST_C_6_1' => $params['post_C_6_1'],
                'POST_C_6_2' => $params['post_C_6_2'],
                'POST_C_6_3' => $params['post_C_6_3'],
                'POST_C_6_4' => $params['post_C_6_4'],
                'POST_C_6_5' => $params['post_C_6_5'],
                'POST_C_6_6' => $params['post_C_6_6'],
                'POST_C_6_7' => $params['post_C_6_7'],
                'POST_C_6_8' => $params['post_C_6_8'],
                'POST_C_6_9' => $params['post_C_6_9'],
                'EQA_C_7_1' => $params['eqa_c_7_1'],
                'EQA_C_7_2' => $params['eqa_c_7_2'],
                'EQA_C_7_3' => $params['eqa_c_7_3'],
                'EQA_C_7_4' => $params['eqa_c_7_4'],
                'EQA_C_7_5' => $params['eqa_c_7_5'],
                'EQA_C_7_6' => $params['eqa_c_7_6'],
                'EQA_C_7_7' => $params['eqa_c_7_7'],
                'EQA_C_7_8' => $params['eqa_c_7_8'],
                'EQA_C_7_9' => $params['eqa_c_7_9'],
                'EQA_C_7_10' => $params['eqa_c_7_10'],
                'EQA_C_7_11' => $params['eqa_c_7_11'],
                'EQA_C_7_12' => $params['eqa_c_7_12'],
                'EQA_C_7_13' => $params['eqa_c_7_13'],
                'EQA_C_7_14' => $params['eqa_c_7_14'],
                'Latitude' => $params['latitude'],
                'Longitude' => $params['longitude'],
                
                'staffaudited' => $params['staffAuditedName'],
                'durationaudit' => $params['durationaudit'],
                //'FINAL_AUDIT_SCORE' => $params['totalPointsScored'],
                //'MAX_AUDIT_SCORE' => $params['totalScoreExpect'],
                //'AUDIT_SCORE_PERCANTAGE' => $params['auditScorePercentage'],
                'correctiveaction' => $summationData
            );
            $result = $this->update($data, array('id' =>$formId));
            
            return $formId;
        }
        
    }
    public function fetchSpiV3FormAuditNo(){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                               ->columns(array(new Expression('DISTINCT(auditroundno) as auditroundno'),'rowCount' => new Expression("COUNT('auditroundno')")))
                               ->group('auditroundno')
                               ->order("auditroundno ASC");
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return $rResult;
    }
    
    public function fetchSpiV3FormFacilityAuditNo($params){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->columns(array(new Expression('DISTINCT(auditroundno) as auditroundno'),'rowCount' => new Expression("COUNT('auditroundno')")))
                                ->where(array($params['fieldName']=>$params['val']))
                                ->group('auditroundno')
                                ->order("auditroundno ASC");
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        return $rResult;
    }
    
    public function mergeFacilityName($params){
        $result = 0;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $facilityDb= new \Application\Model\SpiRtFacilitiesTable($this->adapter);
        if(isset($params['editFacilityName']) && trim($params['editFacilityName'])!= ''){
            $facilityQuery = $sql->select()->from(array('spirt3' => 'spi_rt_3_facilities'))->columns(array('facility_name'))
                                           ->where(array('spirt3.facility_name'=>$params['dafaultFacilityName']));
            $facilityQueryStr = $sql->getSqlStringForSqlObject($facilityQuery);
            $facilityResult = $dbAdapter->query($facilityQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            if($facilityResult){
                $data = array(
                              'facility_id'=>$params['facilityId'],
                              'facility_name'=>$params['editFacilityName']
                            );
                $facilityDb->update($data,array('facility_name'=>$params['dafaultFacilityName']));
            }
            
            $aQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->columns(array('facilityname'))
                                    ->where(array('spiv3.facilityname'=>$params['dafaultFacilityName']));
            $aQueryStr = $sql->getSqlStringForSqlObject($aQuery);
            $aResult = $dbAdapter->query($aQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            if($aResult){
                $data = array(
                              'facilityid'=>$params['facilityId'],
                              'facilityname'=>$params['editFacilityName']
                            );
                $this->update($data,array('facilityname'=>$params['dafaultFacilityName']));
            }
        }
        $c = count($params['upFaciltyName']);
        for($i=0;$i<$c;$i++){
            $aQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->columns(array('facilityname','id'))
                                    ->where(array('spiv3.facilityname'=>$params['upFaciltyName'][$i]));
            $aQueryStr = $sql->getSqlStringForSqlObject($aQuery);
            $aResult = $dbAdapter->query($aQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            
            if(count($aResult)>0){
                for($k=0;$k<count($aResult);$k++){
                    $data = array(
                       'facilityid'=>$params['facilityId'],
                       'facilityname'=>$params['editFacilityName']
                    );
                    $result =  $this->update($data,array('id'=>$aResult[$k]['id']));
                }
            }
            //Update status in Facility table
            $facilityQuery = $sql->select()->from(array('spirt3' => 'spi_rt_3_facilities'))->columns(array('facility_name'))
                                           ->where(array('spirt3.facility_name'=>$params['upFaciltyName'][$i]));
            $facilityQueryStr = $sql->getSqlStringForSqlObject($facilityQuery);
            $facilityResult = $dbAdapter->query($facilityQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            if($facilityResult){
                $facilityDb->update(array('status'=>'deleted'),array('facility_name'=>$params['upFaciltyName'][$i]));
            }
        }
        return $result;
    }
    
    //get all faciltiy name
    public function fetchAllFacilityNames(){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $uQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->columns(array('facilityname'=>new Expression("DISTINCT facilityname")));
        $uQueryStr = $sql->getSqlStringForSqlObject($uQuery);
        $uResult = $dbAdapter->query($uQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        $aQuery = $sql->select()->from(array('spirt3' => 'spi_rt_3_facilities'))
                                ->where('spirt3.status != "deleted"');
        $aQueryStr = $sql->getSqlStringForSqlObject($aQuery);
        $aResult = $dbAdapter->query($aQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        
        return array('uniqueName'=>$uResult,'allName'=>$aResult);
    }
    
    public function fetchAllTestingPointsBasedOnFacility($parameters,$acl){
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	
        $aColumns = array('facilityid','facilityname','testingpointname',"DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')",'AUDIT_SCORE_PERCANTAGE');
        $orderColumns = array('facilityid','facilityname','testingpointname','assesmentofaudit','AUDIT_SCORE_PERCANTAGE');

        /*
        * Paging
        */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
        * Ordering
        */

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
 
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
        * SQL queries
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $start_date = "";
        $end_date = "";
        $sQuery = $sql->select()->from('spi_form_v_3')->columns(array('id','facilityid','facilityname','testingpointname','testingpointtype','assesmentofaudit','AUDIT_SCORE_PERCANTAGE'));
        
        if(isset($parameters['fieldName']) && $parameters['fieldName']=='facilityId'){
            $sQuery=$sQuery->where(array('facilityid'=>$parameters['val']));
        }
        else if(isset($parameters['fieldName']) && $parameters['fieldName']=='facilityName'){
            $sQuery=$sQuery->where(array('facilityname'=>$parameters['val']));
        }
        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }
 
        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }
 
        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance 
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $tQuery =  $sql->select()->from('spi_form_v_3');
        if(isset($parameters['fieldName']) && $parameters['fieldName']=='facilityId'){
            $tQuery=$tQuery->where(array('facilityid'=>$parameters['val']));
        }
        else if(isset($parameters['fieldName']) && $parameters['fieldName']=='facilityName'){
            $tQuery=$tQuery->where(array('facilityname'=>$parameters['val']));
        }
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
           "sEcho" => intval($parameters['sEcho']),
           "iTotalRecords" => $iTotal,
           "iTotalDisplayRecords" => $iFilteredTotal,
           "aaData" => array()
        );
		$loginContainer = new Container('credo');
                $role = $loginContainer->roleCode;
                if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'download-pdf')) {
                    $downloadPdfAction = true;
                } else {
                    $downloadPdfAction = false;
                }
		$commonService = new \Application\Service\CommonService();
		foreach ($rResult as $aRow) {
		 $row = array();
		 $downloadPdf="";
		 
		 $row[] = $aRow['facilityid'];
		 $row[] = ucwords($aRow['facilityname']);
                 $row[] = $aRow['testingpointtype'];
		 $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
		 $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
                if($downloadPdfAction){
                    $downloadPdf = '<br><a href="javascript:void(0);" onclick="downloadPdf('.$aRow['id'].')" style="white-space:nowrap;"><i class="fa fa-download"></i> PDF</a>';
                }
                $row[] = $downloadPdf;
		 $output['aaData'][] = $row;
		}
		return $output;
    }
    
    public function fetchFacilitiesAudits($params){
        $audits = array();
        $aResult = "";
        if(isset($params['facilityName']) && trim($params['facilityName'])!= '') {
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);
            $query = $sql->select()->from(array('spiv3'=>'spi_form_v_3'))
                                   ->columns(array('id','testingpointname','assesmentofaudit'))
                                   ->where(array('spiv3.facilityname'=>$params['facilityName'],'spiv3.status'=>'approved'));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $audits = $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            
            $aQuery = $sql->select()->from(array('spiv3_fclt' => 'spi_rt_3_facilities'))
                                    ->columns(array('facility_name','email'))
                                    ->where(array('spiv3_fclt.facility_name'=>$params['facilityName']));
            $aQueryStr = $sql->getSqlStringForSqlObject($aQuery);
            $aResult = $dbAdapter->query($aQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        }
      return array('audits'=>$audits,'facilityProfile'=>$aResult);
    }
    
    public function getSpiV3PendingCount(){
        $logincontainer = new Container('credo');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where('spiv3.status = "pending"');
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        return count($rResult);
    }
    
    public function deleteAuditRowData($params){
        $result = 0;
        if (trim($params['deleteId']) != "" && trim($params['deleteId'])!= '') {
            $data = array('status' => 'deleted');
            $result = $this->update($data, array('id' => $params['deleteId']));
        }
      return $result;
    }
   
    
    public function fetchAllApprovedSubmissionsDetailsBasedOnAuditDate($parameters,$acl){
        $logincontainer = new Container('credo');
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	
        $aColumns = array('facilityname','auditroundno','assesmentofaudit' , 'testingpointname' ,'testingpointtype','level','affiliation','AUDIT_SCORE_PERCANTAGE','status');
        $orderColumns = array('facilityname','auditroundno','assesmentofaudit', 'testingpointname' ,'testingpointtype','level','affiliation','AUDIT_SCORE_PERCANTAGE','status');

        /*
        * Paging
        */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
        * Ordering
        */

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
 
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
        * SQL queries
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $start_date = "";
        $end_date = "";
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where('spiv3.status != "deleted"');
       
        $tQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                 ->where('spiv3.status != "deleted"');
                                 
        if(isset($parameters['assesmentOfAuditDate']) && $parameters['assesmentOfAuditDate']!=''){
            $sQuery = $sQuery->where("spiv3.assesmentofaudit='".$this->dateFormat($parameters['assesmentOfAuditDate'])."'");
            $tQuery = $tQuery->where("spiv3.assesmentofaudit='".$this->dateFormat($parameters['assesmentOfAuditDate'])."'");
        }
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
            $tQuery = $tQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }
 
        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }
 
        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance 
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
               "sEcho" => intval($parameters['sEcho']),
               "iTotalRecords" => $iTotal,
               "iTotalDisplayRecords" => $iFilteredTotal,
               "aaData" => array()
        );
        
        $role = $logincontainer->roleCode;
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'download-pdf')) {
            $downloadPdfAction = true;
        } else {
            $downloadPdfAction = false;
        }
        
        if ($acl->isAllowed($role, 'Application\Controller\SpiV3', 'approve-status')) {
            $approveStatusAction = true;
        } else {
            $approveStatusAction = false;
        }
        
        $commonService = new \Application\Service\CommonService();
        foreach ($rResult as $aRow) {
         $row = array();
         $downloadPdf="";
         
         
         if(isset($aRow['level_other']) && $aRow['level_other'] != ""){
             $level = " - " .$aRow['level_other'];
         }else{
             $level = '';
         }
         
         $row[] = $aRow['facilityname'];
         $row[] = $aRow['auditroundno'];
         $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
         $row[] = (isset($aRow['testingpointname']) && $aRow['testingpointname'] != "" ? $aRow['testingpointname'] : $aRow['testingpointtype']);
         $row[] = $aRow['testingpointtype'];
         $row[] = $aRow['level'].$level;
         $row[] = $aRow['affiliation'];
         $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
         $row[] = ucwords($aRow['status']);
         //$print = '<a href="/spi-v3/print/' . $aRow['id'] . '" target="_blank" style="white-space:nowrap;"><i class="fa fa-print"></i> Print</a>';
       
         
        if($downloadPdfAction){
            $downloadPdf = '<br><a href="javascript:void(0);" onclick="downloadPdf('.$aRow['id'].')" style="white-space:nowrap;"><i class="fa fa-download"></i> PDF</a>';
        }
         //$pending = '<br><a href="/spi-v3/edit/' . $aRow['id'] . '" style="white-space:nowrap;"><i class="fa fa-pencil"></i> Edit</a>';
         $row[] = $downloadPdf;
         $output['aaData'][] = $row;
        }
        return $output;
    }
    
    public function fetchSpiV3FormUniqueTokens(){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $tokenQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array(new Expression('DISTINCT(token) as token')))
                                    ->group('token')
                                    ->order("token ASC");
        $tokenQueryStr = $sql->getSqlStringForSqlObject($tokenQuery);
        return $dbAdapter->query($tokenQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function fetchViewDataDetails($parameters){
        $logincontainer = new Container('credo');
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	if($parameters['source'] == 'hv') {
            $aColumns = array("DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')",'facilityname','testingpointname','avgMonthTesting','NumberofTester');
            $orderColumns = array('assesmentofaudit','facilityname','testingpointname','avgMonthTesting','NumberofTester','AUDIT_SCORE_PERCANTAGE');
        }else if($parameters['source'] == 'la'){
            $aColumns = array("DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')",'facilityname','testingpointname','avgMonthTesting','AUDIT_SCORE_PERCANTAGE');
            $orderColumns = array('assesmentofaudit','facilityname','testingpointname','avgMonthTesting','AUDIT_SCORE_PERCANTAGE');
        }else if($parameters['source'] == 'ad'){
            $aColumns = array("DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')","DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')");
            $orderColumns = array('assesmentofaudit','assesmentofaudit');
        }else if($parameters['source'] == 'apall' || $parameters['source'] == 'apl180' || $parameters['source'] == 'ap'){
            $aColumns = array('facilityid','facilityname','AUDIT_SCORE_PERCANTAGE',"DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')",'testingpointtype','testingpointname','testingpointtype_other','level','affiliation','AUDIT_SCORE_PERCANTAGE','AUDIT_SCORE_PERCANTAGE');
            $orderColumns = array('facilityid','facilityname','AUDIT_SCORE_PERCANTAGE','assesmentofaudit','testingpointtype','testingpointname','level','affiliation','AUDIT_SCORE_PERCANTAGE','AUDIT_SCORE_PERCANTAGE');
        }else if($parameters['source'] == 'apspi'){
            $aColumns = array("DATE_FORMAT(assesmentofaudit,'%d-%b-%Y')",'PERSONAL_SCORE','PHYSICAL_SCORE','SAFETY_SCORE','PRETEST_SCORE','TEST_SCORE','POST_SCORE','EQA_SCORE');
            $orderColumns = array('assesmentofaudit','PERSONAL_SCORE','PHYSICAL_SCORE','SAFETY_SCORE','PRETEST_SCORE','TEST_SCORE','POST_SCORE','EQA_SCORE');
        }

        /*
        * Paging
        */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
        * Ordering
        */

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
 
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
        * SQL queries
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $start_date = "";
        $end_date = "";
        if (isset($parameters['drange']) && ($parameters['drange'] != "")) {
            $dateField = explode(" ", $parameters['drange']);
            if (isset($dateField[0]) && trim($dateField[0]) != "") {
                $start_date = $this->dateFormat($dateField[0]);                
            }
            if (isset($dateField[2]) && trim($dateField[2]) != "") {
                $end_date = $this->dateFormat($dateField[2]);
            }
        }
    
        if($parameters['source'] == 'ad'){
            //For Audit Dates
            $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array(new Expression('DISTINCT(assesmentofaudit) as assesmentofaudit'),'totalDataPoints' => new \Zend\Db\Sql\Expression("COUNT(*)")))
                                    ->where(array('spiv3.status'=>'approved'))
                                    ->group('spiv3.assesmentofaudit');
            $tQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array(new Expression('DISTINCT(assesmentofaudit) as assesmentofaudit'),'totalDataPoints' => new \Zend\Db\Sql\Expression("COUNT(*)")))
                                    ->where(array('spiv3.status'=>'approved'))
                                    ->group('spiv3.assesmentofaudit');
        }else if($parameters['source'] == 'apall' || $parameters['source'] == 'apl180' || $parameters['source'] == 'ap'){
            if (isset($parameters['date']) && ($parameters['date'] != "")) {
                $dateField = explode(" ", $parameters['date']);
                //print_r($proceed_date);die;
                if (isset($dateField[0]) && trim($dateField[0]) != "") {
                    $start_date = $this->dateFormat($dateField[0]);                
                }
                if (isset($dateField[2]) && trim($dateField[2]) != "") {
                    $end_date = $this->dateFormat($dateField[2]);
                }
            }
            //For Audit Performance Row
            $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array('facilityid','facilityname','auditroundno','assesmentofaudit','testingpointtype','testingpointname','testingpointtype_other','level','affiliation','AUDIT_SCORE_PERCANTAGE'))
                                    ->where(array('spiv3.status'=>'approved'));
            
            $tQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array('facilityid','facilityname','auditroundno','assesmentofaudit','testingpointtype','testingpointname','testingpointtype_other','level','affiliation','AUDIT_SCORE_PERCANTAGE'))
                                    ->where(array('spiv3.status'=>'approved'));
                                    
            if($parameters['source'] == 'apl180'){
                $sQuery = $sQuery->where("(`assesmentofaudit` BETWEEN CURDATE() - INTERVAL 180 DAY AND CURDATE())");
                $tQuery = $tQuery->where("(`assesmentofaudit` BETWEEN CURDATE() - INTERVAL 180 DAY AND CURDATE())");
            }
            if(isset($parameters['testPoint']) && trim($parameters['testPoint'])!=''){
               $sQuery = $sQuery->where("spiv3.testingpointtype='".$parameters['testPoint']."'");
               $tQuery = $tQuery->where("spiv3.testingpointtype='".$parameters['testPoint']."'");
               if(isset($parameters['testPointName']) && trim($parameters['testPointName'])!= ''){
                    if(trim($parameters['testPoint'])!= 'other'){
                       $sQuery = $sQuery->where("spiv3.testingpointname='".$parameters['testPointName']."'");
                       $tQuery = $tQuery->where("spiv3.testingpointname='".$parameters['testPointName']."'");
                    }else{
                       $sQuery = $sQuery->where("spiv3.testingpointtype_other='".$parameters['testPointName']."'");
                       $tQuery = $tQuery->where("spiv3.testingpointtype_other='".$parameters['testPointName']."'");
                    }
               }
            }
            if(isset($parameters['auditRndNo']) && $parameters['auditRndNo']!=''){
               $sQuery = $sQuery->where("spiv3.auditroundno='".$parameters['auditRndNo']."'");
               $tQuery = $tQuery->where("spiv3.auditroundno='".$parameters['auditRndNo']."'");
            }
            if(isset($parameters['scoreLevel']) && $parameters['scoreLevel']!=''){
              if($parameters['scoreLevel'] == 0){
                $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
                $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE < 40");
              }else if($parameters['scoreLevel'] == 1){
                $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
                $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 40 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 59");
              }else if($parameters['scoreLevel'] == 2){
                $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
                $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 60 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 79");
              }else if($parameters['scoreLevel'] == 3){
                $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
                $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 80 AND spiv3.AUDIT_SCORE_PERCANTAGE <= 89");
              }else if($parameters['scoreLevel'] == 4){
                $sQuery = $sQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
                $tQuery = $tQuery->where("spiv3.AUDIT_SCORE_PERCANTAGE >= 90");
              }
            }
            if(isset($parameters['level']) && $parameters['level']!=''){
               $sQuery = $sQuery->where("spiv3.level='".$parameters['level']."'");
               $tQuery = $tQuery->where("spiv3.level='".$parameters['level']."'");
            }
            if(isset($parameters['province']) && $parameters['province']!=''){
                $provinces = explode(",",$parameters['province']);
                $sQuery = $sQuery->where('spiv3.level_name IN ("' . implode('", "', $provinces) . '")');
                $tQuery = $tQuery->where('spiv3.level_name IN ("' . implode('", "', $provinces) . '")');
            }
            if(isset($parameters['affiliation']) && $parameters['affiliation']!=''){
              $sQuery = $sQuery->where("spiv3.affiliation='".$parameters['affiliation']."'");
              $tQuery = $tQuery->where("spiv3.affiliation='".$parameters['affiliation']."'");
            }
        }else if($parameters['source'] == 'apspi'){
            //For Audit Performance
            $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array('assesmentofaudit','PERSONAL_SCORE' => new Expression('AVG(PERSONAL_SCORE)'),'PHYSICAL_SCORE' => new Expression('AVG(PHYSICAL_SCORE)'),'SAFETY_SCORE' => new Expression('AVG(SAFETY_SCORE)'),'PRETEST_SCORE' => new Expression('AVG(PRETEST_SCORE)'),'TEST_SCORE' => new Expression('AVG(TEST_SCORE)'),'POST_SCORE' => new Expression('AVG(POST_SCORE)'),'EQA_SCORE' => new Expression('AVG(EQA_SCORE)')))
                                    ->group('spiv3.assesmentofaudit');
            $tQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array('assesmentofaudit','PERSONAL_SCORE' => new Expression('AVG(PERSONAL_SCORE)'),'PHYSICAL_SCORE' => new Expression('AVG(PHYSICAL_SCORE)'),'SAFETY_SCORE' => new Expression('AVG(SAFETY_SCORE)'),'PRETEST_SCORE' => new Expression('AVG(PRETEST_SCORE)'),'TEST_SCORE' => new Expression('AVG(TEST_SCORE)'),'POST_SCORE' => new Expression('AVG(POST_SCORE)'),'EQA_SCORE' => new Expression('AVG(EQA_SCORE)')))
                                    ->group('spiv3.assesmentofaudit');
            
            if(isset($parameters['roundno']) && $parameters['roundno']!=''){
              $xplodRoundNo = explode(",",$parameters['roundno']);
              $sQuery = $sQuery->where('spiv3.auditroundno IN ("' . implode('", "', $xplodRoundNo) . '")');
              $tQuery = $tQuery->where('spiv3.auditroundno IN ("' . implode('", "', $xplodRoundNo) . '")');
            }
        }else{
            //For Others
            $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array('assesmentofaudit','facilityname','testingpointname','testingpointtype','avgMonthTesting','NumberofTester','AUDIT_SCORE_PERCANTAGE'));
       
            $tQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                     ->columns(array('assesmentofaudit','facilityname','testingpointname','testingpointtype','avgMonthTesting','NumberofTester','AUDIT_SCORE_PERCANTAGE'));
        }
        
        if(isset($logincontainer->token) && count($logincontainer->token) > 0){
            $sQuery = $sQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
            $tQuery = $tQuery->where('spiv3.token IN ("' . implode('", "', $logincontainer->token) . '")');
        }
        
        if (trim($start_date) != "" && trim($end_date) != "") {
            $sQuery = $sQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
            $tQuery = $tQuery->where(array("spiv3.assesmentofaudit >='" . $start_date ."'", "spiv3.assesmentofaudit <='" . $end_date."'"));
        }
        
        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }
 
        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }
 
        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        
        $commonService = new \Application\Service\CommonService();
        //$personalScoreArray = array();
        //$physicalScoreArray = array();
        //$safetyScoreArray = array();
        //$preTestScoreArray = array();
        //$testScoreArray = array();
        //$postTestScoreArray = array();
        //$eqaScoreArray = array();
        //$personalScore = 0;
        //$physicalScore = 0;
        //$safetyScore = 0;
        //$preTestScore = 0;
        //$testScore = 0;
        //$postTestScore = 0;
        //$eqaScore = 0;
        $auditScore = 0;
        $levelZero = array();
        $levelOne = array();
        $levelTwo = array();
        $levelThree = array();
        $levelFour = array();
        foreach ($rResult as $aRow) {
          $row = array();
            if($parameters['source'] == 'hv' || $parameters['source'] == 'la' || $parameters['source'] == 'apall' || $parameters['source'] == 'apl180' || $parameters['source'] == 'ap'){
                $auditScore+=$aRow['AUDIT_SCORE_PERCANTAGE'];
                if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] < 40){
                  $level = 0;
                  $levelZero[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
                }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 40 && $aRow['AUDIT_SCORE_PERCANTAGE'] <60){
                  $level = 1;
                  $levelOne[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
                }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 60 && $aRow['AUDIT_SCORE_PERCANTAGE'] <80){
                  $level = 2;
                  $levelTwo[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
                }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 80 && $aRow['AUDIT_SCORE_PERCANTAGE'] <90){
                  $level = 3;
                  $levelThree[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
                }else if(isset($aRow['AUDIT_SCORE_PERCANTAGE']) && $aRow['AUDIT_SCORE_PERCANTAGE'] >= 90){
                  $level = 4;
                  $levelFour[] = $aRow['AUDIT_SCORE_PERCANTAGE'];
                }
           }
           if($parameters['source'] == 'hv') {   
                $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
                $row[] = ucwords($aRow['facilityname']);
                $row[] = (isset($aRow['testingpointname']) && $aRow['testingpointname'] != "" ? $aRow['testingpointname'] : $aRow['testingpointtype']);
                $row[] = (isset($aRow['avgMonthTesting']) ? $aRow['avgMonthTesting'] : 0);
                $row[] = (isset($aRow['NumberofTester']) ? $aRow['NumberofTester'] : 0);
                $row[] = $level;
          }else if($parameters['source'] == 'la') {
            $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
            $row[] = ucwords($aRow['facilityname']);
            $row[] = (isset($aRow['testingpointname']) && $aRow['testingpointname'] != "" ? $aRow['testingpointname'] : $aRow['testingpointtype']);
            $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
            $row[] = (isset($aRow['avgMonthTesting']) ? $aRow['avgMonthTesting'] : 0);
          }else if($parameters['source'] == 'ad') {
            $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
            $row[] = $aRow['totalDataPoints'];
          }else if($parameters['source'] == 'apall' || $parameters['source'] == 'apl180' || $parameters['source'] == 'ap') {
            $row[] = $aRow['facilityid'];
            $row[] = ucwords($aRow['facilityname']);
            $row[] = $aRow['auditroundno'];
            $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
            $row[] = ucwords($aRow['testingpointtype']);
            $row[] = ($aRow['testingpointtype'] == 'other')?ucwords($aRow['testingpointtype_other']):ucwords($aRow['testingpointname']);
            $row[] = ucwords($aRow['level']);
            $row[] = ucwords($aRow['affiliation']);
            $row[] = $level;
            $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
          }else if($parameters['source'] == 'apspi') {
            $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
            $row[] = round($aRow['PERSONAL_SCORE'],2);
            $row[] = round($aRow['PHYSICAL_SCORE'],2);
            $row[] = round($aRow['SAFETY_SCORE'],2);
            $row[] = round($aRow['PRETEST_SCORE'],2);
            $row[] = round($aRow['TEST_SCORE'],2);
            $row[] = round($aRow['POST_SCORE'],2);
            $row[] = round($aRow['EQA_SCORE'],2);
            //$personalScoreArray[] = $aRow['PERSONAL_SCORE'];
            //$physicalScoreArray[] = $aRow['PHYSICAL_SCORE'];
            //$safetyScoreArray[] = $aRow['SAFETY_SCORE'];
            //$preTestScoreArray[] = $aRow['PRETEST_SCORE'];
            //$testScoreArray[] = $aRow['TEST_SCORE'];
            //$postTestScoreArray[] = $aRow['POST_SCORE'];
            //$eqaScoreArray[] = $aRow['EQA_SCORE'];
            //$personalScore+=$aRow['PERSONAL_SCORE'];
            //$physicalScore+=$aRow['PHYSICAL_SCORE'];
            //$safetyScore+=$aRow['SAFETY_SCORE'];
            //$preTestScore+=$aRow['PRETEST_SCORE'];
            //$testScore+=$aRow['TEST_SCORE'];
            //$postTestScore+=$aRow['POST_SCORE'];
            //$eqaScore+=$aRow['EQA_SCORE'];
          }
         $output['aaData'][] = $row;
        }
         $output['avgAuditScore'] = (count($rResult) > 0) ? round($auditScore/count($rResult),2) : 0;
         $output['levelZeroCount'] = count($levelZero);
         $output['levelOneCount'] = count($levelOne);
         $output['levelTwoCount'] = count($levelTwo);
         $output['levelThreeCount'] = count($levelThree);
         $output['levelFourCount'] = count($levelFour);
        return $output;
    }
    
    public function fetchAllTestingPointType(){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                    ->columns(array(new Expression('DISTINCT(testingPointType) as testingPointType')))
                                    ->group('testingPointType')
                                    ->order("testingPointType ASC");
        $queryStr = $sql->getSqlStringForSqlObject($query);
        
        return $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function fetchTestingPointTypeNamesByType($params){
        $typeResult = array();
        if(isset($params['testingPointType']) && trim($params['testingPointType'])!= ''){
            if($params['testingPointType'] == 'other'){
                $column = 'DISTINCT(testingpointtype_other) as testingpointName';
            }else{
               $column = 'DISTINCT(testingpointname) as testingpointName'; 
            }
            $dbAdapter = $this->adapter;
            $sql = new Sql($dbAdapter);
            $query = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                        ->columns(array(new Expression($column)))
                                        ->where(array('testingpointtype'=>$params['testingPointType']));
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $typeResult = $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        }
      return $typeResult;
    }
    
    public function fetchSpiV3FormUniqueLevelNames(){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->columns(array('facilityname'))
                               ->join(array('f'=>'spi_rt_3_facilities'),'f.facility_name=spiv3.facilityname',array('province','district'))
                               ->group("province")
                               ->order("province ASC");
        $queryStr = $sql->getSqlStringForSqlObject($query);
        //$query = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
        //                       ->columns(array(new Expression('DISTINCT(level_name) as level_name')))
        //                       ->order("level_name ASC");
        //$queryStr = $sql->getSqlStringForSqlObject($query);
      return $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    public function fetchDistrictData($params)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('f' => 'spi_rt_3_facilities'))
                                ->group('district');
        if (is_array($params['province'])){
            $query = $query->where('f.province IN ("' . implode('", "', $params['province']) . '") AND f.district!=""');
        }else{
            $query = $query->where('f.province="'.$params['province'].'" AND f.district!=""');
        }
        $queryStr = $sql->getSqlStringForSqlObject($query);
        return $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function addValidateSpiv3Data($params)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $vId = explode(",",$params['validateId']);
        $newQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3_temp'))
                            ->where('spiv3.id IN ("' . implode('", "', $vId) . '")');
        $newQueryStr = $sql->getSqlStringForSqlObject($newQuery); // Get the string of the Sql, instead of the Select-instance
        $totalResult = $dbAdapter->query($newQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        if($totalResult){
            foreach($totalResult as $newResult){
                $insert = $sql->insert('spi_form_v_3');
                if(isset($newResult['testingpointname']) && trim($newResult['testingpointname'])==""){
                    $newResult['testingpointname']=$newResult['testingpointtype'];
                }
                $newResult['instanceID'] = isset($newResult['instanceID']) ? $newResult['instanceID'] :"";
                $newResult['instanceName'] = isset($newResult['instanceName']) ? $newResult['instanceName'] :"";
        
                $par = array(
                        'token' => $params['token'],
                        'content' => $newResult['content'],
                        'formId' => $newResult['formId'],
                        'formVersion' => $newResult['formVersion'],
                        'meta-instance-id' => $newResult['instanceID'],
                        'meta-model-version' => $newResult['meta-model-version'],
                        'meta-ui-version' => $newResult['meta-ui-version'],
                        'meta-submission-date' => $newResult['meta-submission-date'],
                        'meta-is-complete' => $newResult['meta-is-complete'],
                        'meta-date-marked-as-complete' => $newResult['meta-date-marked-as-complete'],
                        'start' => $newResult['start'],
                        'end' => $newResult['end'],
                        'today' => $newResult['today'],
                        'deviceid' => $newResult['deviceid'],
                        'subscriberid' => $newResult['subscriberid'],
                        'text_image' => $newResult['text_image'],
                        'info1' => $newResult['info1'],
                        'info2' => $newResult['info2'],
                        'assesmentofaudit' => $newResult['assesmentofaudit'],
                        'auditroundno' => $newResult['auditroundno'],
                        'facilityname' => $newResult['facilityname'],
                        'facilityid' => $newResult['facilityid'],
                        'testingpointname' => $newResult['testingpointname'],
                        'testingpointtype' => $newResult['testingpointtype'],
                        'testingpointtype_other' => $newResult['testingpointtype_other'],
                        'locationaddress' => $newResult['locationaddress'],
                        'level' => $newResult['level'],
                        'level_other' => $newResult['level_other'],
                        'level_name' => $newResult['level_name'],
                        'affiliation' => $newResult['affiliation'],
                        'affiliation_other' => $newResult['affiliation_other'],
                        'NumberofTester' => (isset($newResult['NumberofTester']) && $newResult['NumberofTester'] > 0 ? $newResult['NumberofTester'] : 0),
                        'avgMonthTesting' => (isset($newResult['avgMonthTesting']) && $newResult['avgMonthTesting'] > 0 ? $newResult['avgMonthTesting'] : 0),
                        'name_auditor_lead' => $newResult['name_auditor_lead'],
                        'name_auditor2' => $newResult['name_auditor2'],
                        'info4' => $newResult['info4'],
                        'INSTANCE' => $newResult['INSTANCE'],
                        'PERSONAL_Q_1_1' => $newResult['PERSONAL_Q_1_1'],
                        'PERSONAL_C_1_1' => $newResult['PERSONAL_C_1_1'],
                        'PERSONAL_Q_1_2' => $newResult['PERSONAL_Q_1_2'],
                        'PERSONAL_C_1_2' => $newResult['PERSONAL_C_1_2'],
                        'PERSONAL_Q_1_3' => $newResult['PERSONAL_Q_1_3'],
                        'PERSONAL_C_1_3' => $newResult['PERSONAL_C_1_3'],
                        'PERSONAL_Q_1_4' => $newResult['PERSONAL_Q_1_4'],
                        'PERSONAL_C_1_4' => $newResult['PERSONAL_C_1_4'],
                        'PERSONAL_Q_1_5' => $newResult['PERSONAL_Q_1_5'],
                        'PERSONAL_C_1_5' => $newResult['PERSONAL_C_1_5'],
                        'PERSONAL_Q_1_6' => $newResult['PERSONAL_Q_1_6'],
                        'PERSONAL_C_1_6' => $newResult['PERSONAL_C_1_6'],
                        'PERSONAL_Q_1_7' => $newResult['PERSONAL_Q_1_7'],
                        'PERSONAL_C_1_7' => $newResult['PERSONAL_C_1_7'],
                        'PERSONAL_Q_1_8' => $newResult['PERSONAL_Q_1_8'],
                        'PERSONAL_C_1_8' => $newResult['PERSONAL_C_1_8'],
                        'PERSONAL_Q_1_9' => $newResult['PERSONAL_Q_1_9'],
                        'PERSONAL_C_1_9' => $newResult['PERSONAL_C_1_9'],
                        'PERSONAL_Q_1_10' => $newResult['PERSONAL_Q_1_10'],
                        'PERSONAL_C_1_10' => $newResult['PERSONAL_C_1_10'],
                        'PERSONAL_SCORE' => $newResult['PERSONAL_SCORE'],
                        'PERSONAL_Display' => $newResult['PERSONAL_Display'],
                        'PERSONALPHOTO' => $newResult['PERSONALPHOTO'],
                        'PHYSICAL_Q_2_1' => $newResult['PHYSICAL_Q_2_1'],
                        'PHYSICAL_C_2_1' => $newResult['PHYSICAL_C_2_1'],
                        'PHYSICAL_Q_2_2' => $newResult['PHYSICAL_Q_2_2'],
                        'PHYSICAL_C_2_2' => $newResult['PHYSICAL_C_2_2'],
                        'PHYSICAL_Q_2_3' => $newResult['PHYSICAL_Q_2_3'],
                        'PHYSICAL_C_2_3' => $newResult['PHYSICAL_C_2_3'],
                        'PHYSICAL_Q_2_4' => $newResult['PHYSICAL_Q_2_4'],
                        'PHYSICAL_C_2_4' => $newResult['PHYSICAL_C_2_4'],
                        'PHYSICAL_Q_2_5' => $newResult['PHYSICAL_Q_2_5'],
                        'PHYSICAL_C_2_5' => $newResult['PHYSICAL_C_2_5'],
                        'PHYSICAL_SCORE' => $newResult['PHYSICAL_SCORE'],
                        'PHYSICAL_Display' => $newResult['PHYSICAL_Display'],
                        'PHYSICALPHOTO' => $newResult['PHYSICALPHOTO'],
                        'SAFETY_Q_3_1' => $newResult['SAFETY_Q_3_1'],
                        'SAFETY_C_3_1' => $newResult['SAFETY_C_3_1'],
                        'SAFETY_Q_3_2' => $newResult['SAFETY_Q_3_2'],
                        'SAFETY_C_3_2' => $newResult['SAFETY_C_3_2'],
                        'SAFETY_Q_3_3' => $newResult['SAFETY_Q_3_3'],
                        'SAFETY_C_3_3' => $newResult['SAFETY_C_3_3'],
                        'SAFETY_Q_3_4' => $newResult['SAFETY_Q_3_4'],
                        'SAFETY_C_3_4' => $newResult['SAFETY_C_3_4'],
                        'SAFETY_Q_3_5' => $newResult['SAFETY_Q_3_5'],
                        'SAFETY_C_3_5' => $newResult['SAFETY_C_3_5'],
                        'SAFETY_Q_3_6' => $newResult['SAFETY_Q_3_6'],
                        'SAFETY_C_3_6' => $newResult['SAFETY_C_3_6'],
                        'SAFETY_Q_3_7' => $newResult['SAFETY_Q_3_7'],
                        'SAFETY_C_3_7' => $newResult['SAFETY_C_3_7'],
                        'SAFETY_Q_3_8' => $newResult['SAFETY_Q_3_8'],
                        'SAFETY_C_3_8' => $newResult['SAFETY_C_3_8'],
                        'SAFETY_Q_3_9' => $newResult['SAFETY_Q_3_9'],
                        'SAFETY_C_3_9' => $newResult['SAFETY_C_3_9'],
                        'SAFETY_Q_3_10' => $newResult['SAFETY_Q_3_10'],
                        'SAFETY_C_3_10' => $newResult['SAFETY_C_3_10'],
                        'SAFETY_Q_3_11' => $newResult['SAFETY_Q_3_11'],
                        'SAFETY_C_3_11' => $newResult['SAFETY_C_3_11'],
                        'SAFETY_SCORE' => $newResult['SAFETY_SCORE'],
                        'SAFETY_DISPLAY' => $newResult['SAFETY_DISPLAY'],
                        'SAFETYPHOTO' => $newResult['SAFETYPHOTO'],
                        'PRE_Q_4_1' => $newResult['PRE_Q_4_1'],
                        'PRE_C_4_1' => $newResult['PRE_C_4_1'],
                        'PRE_Q_4_2' => $newResult['PRE_Q_4_2'],
                        'PRE_C_4_2' => $newResult['PRE_C_4_2'],
                        'PRE_Q_4_3' => $newResult['PRE_Q_4_3'],
                        'PRE_C_4_3' => $newResult['PRE_C_4_3'],
                        'PRE_Q_4_4' => $newResult['PRE_Q_4_4'],
                        'PRE_C_4_4' => $newResult['PRE_C_4_4'],
                        'PRE_Q_4_5' => $newResult['PRE_Q_4_5'],
                        'PRE_C_4_5' => $newResult['PRE_C_4_5'],
                        'PRE_Q_4_6' => $newResult['PRE_Q_4_6'],
                        'PRE_C_4_6' => $newResult['PRE_C_4_6'],
                        'PRE_Q_4_7' => $newResult['PRE_Q_4_7'],
                        'PRE_C_4_7' => $newResult['PRE_C_4_7'],
                        'PRE_Q_4_8' => $newResult['PRE_Q_4_8'],
                        'PRE_C_4_8' => $newResult['PRE_C_4_8'],
                        'PRE_Q_4_9' => $newResult['PRE_Q_4_9'],
                        'PRE_C_4_9' => $newResult['PRE_C_4_9'],
                        'PRE_Q_4_10' => $newResult['PRE_Q_4_10'],
                        'PRE_C_4_10' => $newResult['PRE_C_4_10'],
                        'PRE_Q_4_11' => $newResult['PRE_Q_4_11'],
                        'PRE_C_4_11' => $newResult['PRE_C_4_11'],
                        'PRE_Q_4_12' => $newResult['PRE_Q_4_12'],
                        'PRE_C_4_12' => $newResult['PRE_C_4_12'],
                        'PRETEST_SCORE' => $newResult['PRETEST_SCORE'],
                        'PRETEST_Display' => $newResult['PRETEST_Display'],
                        'PRETESTPHOTO' => $newResult['PRETESTPHOTO'],
                        'TEST_Q_5_1' => $newResult['TEST_Q_5_1'],
                        'TEST_C_5_1' => $newResult['TEST_C_5_1'],
                        'TEST_Q_5_2' => $newResult['TEST_Q_5_2'],
                        'TEST_C_5_2' => $newResult['TEST_C_5_2'],
                        'TEST_Q_5_3' => $newResult['TEST_Q_5_3'],
                        'TEST_C_5_3' => $newResult['TEST_C_5_3'],
                        'TEST_Q_5_4' => $newResult['TEST_Q_5_4'],
                        'TEST_C_5_4' => $newResult['TEST_C_5_4'],
                        'TEST_Q_5_5' => $newResult['TEST_Q_5_5'],
                        'TEST_C_5_5' => $newResult['TEST_C_5_5'],
                        'TEST_Q_5_6' => $newResult['TEST_Q_5_6'],
                        'TEST_C_5_6' => $newResult['TEST_C_5_6'],
                        'TEST_Q_5_7' => $newResult['TEST_Q_5_7'],
                        'TEST_C_5_7' => $newResult['TEST_C_5_7'],
                        'TEST_Q_5_8' => $newResult['TEST_Q_5_8'],
                        'TEST_C_5_8' => $newResult['TEST_C_5_8'],
                        'TEST_Q_5_9' => $newResult['TEST_Q_5_9'],
                        'TEST_C_5_9' => $newResult['TEST_C_5_9'],
                        'TEST_SCORE' => $newResult['TEST_SCORE'],
                        'TEST_DISPLAY' => $newResult['TEST_DISPLAY'],
                        'TESTPHOTO' => $newResult['TESTPHOTO'],
                        'POST_Q_6_1' => $newResult['POST_Q_6_1'],
                        'POST_C_6_1' => $newResult['POST_C_6_1'],
                        'POST_Q_6_2' => $newResult['POST_Q_6_2'],
                        'POST_C_6_2' => $newResult['POST_C_6_2'],
                        'POST_Q_6_3' => $newResult['POST_Q_6_3'],
                        'POST_C_6_3' => $newResult['POST_C_6_3'],
                        'POST_Q_6_4' => $newResult['POST_Q_6_4'],
                        'POST_C_6_4' => $newResult['POST_C_6_4'],
                        'POST_Q_6_5' => $newResult['POST_Q_6_5'],
                        'POST_C_6_5' => $newResult['POST_C_6_5'],
                        'POST_Q_6_6' => $newResult['POST_Q_6_6'],
                        'POST_C_6_6' => $newResult['POST_C_6_6'],
                        'POST_Q_6_7' => $newResult['POST_Q_6_7'],
                        'POST_C_6_7' => $newResult['POST_C_6_7'],
                        'POST_Q_6_8' => $newResult['POST_Q_6_8'],
                        'POST_C_6_8' => $newResult['POST_C_6_8'],
                        'POST_Q_6_9' => $newResult['POST_Q_6_9'],
                        'POST_C_6_9' => $newResult['POST_C_6_9'],
                        'POST_SCORE' => $newResult['POST_SCORE'],
                        'POST_DISPLAY' => $newResult['POST_DISPLAY'],
                        'POSTTESTPHOTO' => $newResult['POSTTESTPHOTO'],
                        'EQA_Q_7_1' => $newResult['EQA_Q_7_1'],
                        'EQA_C_7_1' => $newResult['EQA_C_7_1'],
                        'EQA_Q_7_2' => $newResult['EQA_Q_7_2'],
                        'EQA_C_7_2' => $newResult['EQA_C_7_2'],
                        'EQA_Q_7_3' => $newResult['EQA_Q_7_3'],
                        'EQA_C_7_3' => $newResult['EQA_C_7_3'],
                        'EQA_Q_7_4' => $newResult['EQA_Q_7_4'],
                        'EQA_C_7_4' => $newResult['EQA_C_7_4'],
                        'EQA_Q_7_5' => $newResult['EQA_Q_7_5'],
                        'EQA_C_7_5' => $newResult['EQA_C_7_5'],
                        'EQA_Q_7_6' => $newResult['EQA_Q_7_6'],
                        'EQA_C_7_6' => $newResult['EQA_C_7_6'],
                        'EQA_Q_7_7' => $newResult['EQA_Q_7_7'],
                        'EQA_C_7_7' => $newResult['EQA_C_7_7'],
                        'EQA_Q_7_8' => $newResult['EQA_Q_7_8'],
                        'EQA_C_7_8' => $newResult['EQA_C_7_8'],
                        'sampleretesting' => $newResult['sampleretesting'],
                        'EQA_Q_7_9' => $newResult['EQA_Q_7_9'],
                        'EQA_C_7_9' => $newResult['EQA_C_7_9'],
                        'EQA_Q_7_10' => $newResult['EQA_Q_7_10'],
                        'EQA_C_7_10' => $newResult['EQA_C_7_10'],
                        'EQA_Q_7_11' => $newResult['EQA_Q_7_11'],
                        'EQA_C_7_11' => $newResult['EQA_C_7_11'],
                        'EQA_Q_7_12' => $newResult['EQA_Q_7_12'],
                        'EQA_C_7_12' => $newResult['EQA_C_7_12'],
                        'EQA_Q_7_13' => $newResult['EQA_Q_7_13'],
                        'EQA_C_7_13' => $newResult['EQA_C_7_13'],
                        'EQA_Q_7_14' => $newResult['EQA_Q_7_14'],
                        'EQA_C_7_14' => $newResult['EQA_C_7_14'],
                        'EQA_MAX_SCORE' => $newResult['EQA_MAX_SCORE'],
                        'EQA_REQ' => $newResult['EQA_REQ'],
                        'EQA_OPT' => $newResult['EQA_OPT'],
                        'EQA_SCORE' => $newResult['EQA_SCORE'],
                        'EQA_DISPLAY' => $newResult['EQA_DISPLAY'],
                        'EQAPHOTO' => $newResult['EQAPHOTO'],
                        'FINAL_AUDIT_SCORE' => $newResult['FINAL_AUDIT_SCORE'],
                        'MAX_AUDIT_SCORE' => $newResult['MAX_AUDIT_SCORE'],
                        'AUDIT_SCORE_PERCANTAGE' => $newResult['AUDIT_SCORE_PERCANTAGE'],
                        'staffaudited' => $newResult['staffaudited'],
                        'durationaudit' => $newResult['durationaudit'],
                        'personincharge' => $newResult['personincharge'],
                        'endofsurvey' => $newResult['endofsurvey'],
                        'info5' => $newResult['info5'],
                        'info6' => $newResult['info6'],
                        'info10' => $newResult['info10'],
                        'info11' => $newResult['info11'],
                        'summarypage' => $newResult['summarypage'],
                        'SUMMARY_NOT_AVL' => $newResult['SUMMARY_NOT_AVL'],
                        'info12' => $newResult['info12'],
                        'info17' => $newResult['info17'],
                        'info21' => $newResult['info21'],
                        'info22' => $newResult['info22'],
                        'info23' => $newResult['info23'],
                        'info24' => $newResult['info24'],
                        'info25' => $newResult['info25'],
                        'info26' => $newResult['info26'],
                        'info27' => $newResult['info27'],
                        'correctiveaction' => $newResult['correctiveaction'],
                        'sitephoto' => $newResult['sitephoto'],
                        'Latitude' => $newResult['Latitude'],
                        'Longitude' => $newResult['Longitude'],
                        'Altitude' => $newResult['Altitude'],
                        'Accuracy' => $newResult['Accuracy'],
                        'auditorSignature' => $newResult['auditorSignature'],
                        'instanceID' => $newResult['instanceID'],
                        'instanceName' => $newResult['instanceName'],
                        'status'=>$newResult['status']
                    );
                    $dbAdapter = $this->adapter;
                    $insert->values($par);
                    $selectString = $sql->getSqlStringForSqlObject($insert);
                    $results = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);
                    if($results->getGeneratedValue()>0){
                        $spiv3Temp = new \Application\Model\SpiFormVer3TempTable($this->adapter);
                        $spiv3Temp->delete(array('id'=>$newResult['id']));
                    }
            }
        }
    }
    
    public function updateSpiv3FacilityInfo($id,$params){
        if($id > 0){
	    $data = array(
		'facilityid'=>$params['facilityId'],
		'facilityname'=>$params['facilityName']
	    );
	  $this->update($data,array('facility'=>$id));
	}
      return $id;
    }
}
