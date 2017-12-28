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
class SpiFormVer3DuplicateTable extends AbstractTableGateway {

    protected $table = 'spi_form_v_3_duplicate';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    //remove audit data
    public function removeAuditData($params)
    {
        $result = false;
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $dResult = $dbAdapter->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'spi_form_v_3_duplicate'", $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        if(count($dResult)>0){
        $sQuery = $sql->select()->from(array('spiv3' => 'spi_form_v_3'))
                                ->where('spiv3.id = "'.$params['id'].'"');
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $aResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
            foreach($dResult as $dData){
                $data[$dData['COLUMN_NAME']] = $aResult[$dData['COLUMN_NAME']];
            }
            
            $sql = new Sql($this->adapter);
            $insert = $sql->insert('spi_form_v_3_duplicate');
            $dbAdapter = $this->adapter;
            $result = $insert->values($data);
            $selectString = $sql->getSqlStringForSqlObject($insert);
            $results = $dbAdapter->query($selectString, $dbAdapter::QUERY_MODE_EXECUTE);        
            if($result){
                $spiver3table = new \Application\Model\SpiFormVer3Table($dbAdapter);
                $spiver3table->delete(array('id'=>$params['id']));
                return $params['id'];
            }
        }
        return $result;
    }
}