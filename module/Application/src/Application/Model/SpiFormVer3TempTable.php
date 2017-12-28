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
class SpiFormVer3TempTable extends AbstractTableGateway {

    protected $table = 'spi_form_v_3_temp';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAllValidateSpiv3Details($parameters){
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
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3_temp'))
                                ->where('spiv3.status != "deleted" and spiv3.spi_data_status=0');
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
        $tQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3_temp'))
                                 ->where('spiv3.status != "deleted" and spiv3.spi_data_status=0');
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
        $commonService = new \Application\Service\CommonService();
        foreach ($rResult as $aRow) {
         $row = array();
         if(isset($aRow['level_other']) && $aRow['level_other'] != ""){
            $level = " - " .$aRow['level_other'];
         }else{
            $level = '';
         }
         $row[] = '<input type="checkbox" class="checkSpiv3Data" name="chk[]" id="chk' . $aRow['id'] . '"  value="' . $aRow['id'] . '" onclick="getValidateId(this);"  />';
         $row[] = $aRow['facilityname'];
         $row[] = $aRow['auditroundno'];
         $row[] = $commonService->humanDateFormat($aRow['assesmentofaudit']);
         $row[] = (isset($aRow['testingpointname']) && $aRow['testingpointname'] != "" ? $aRow['testingpointname'] : $aRow['testingpointtype']);
         $row[] = $aRow['testingpointtype'];
         $row[] = $aRow['level'].$level;
         $row[] = $aRow['affiliation'];
         $row[] = round($aRow['AUDIT_SCORE_PERCANTAGE'],2);
         $row[] = ucwords($aRow['status']);
         $output['aaData'][] = $row;
        }
        //get count of exist data
        $totalQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3'))->columns(array('totalData' => new \Zend\Db\Sql\Expression("COUNT(*)")))->where('spiv3.status != "deleted"');
        $totalQueryStr = $sql->getSqlStringForSqlObject($totalQuery); // Get the string of the Sql, instead of the Select-instance
        $totalResult = $dbAdapter->query($totalQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        //get count of new data
        $newQuery =  $sql->select()->from(array('spiv3' => 'spi_form_v_3_temp'))->columns(array('newData' => new \Zend\Db\Sql\Expression("COUNT(id)")));
        $newQueryStr = $sql->getSqlStringForSqlObject($newQuery); // Get the string of the Sql, instead of the Select-instance
        $newResult = $dbAdapter->query($newQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        
        $output['totalData'] = $totalResult['totalData'];
        $output['newData'] = $newResult['newData'];
        return $output;
    }
}