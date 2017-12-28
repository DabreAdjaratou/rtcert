<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\AbstractTableGateway;

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
class SpiFormLabelsTable extends AbstractTableGateway {

    protected $table = 'spi_v3_form_labels';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
      
    public function getAllLabels(){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_v3_form_labels'));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        //echo $sQueryStr;//die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        $response = array();
        foreach($rResult as $row){
            $response[$row['field']] = array($row['short_label'],$row['label']);
        }
        return $response;
    }
    
}
