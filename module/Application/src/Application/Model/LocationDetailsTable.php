<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Application\Service\CommonService;

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
class LocationDetailsTable extends AbstractTableGateway {

    protected $table = 'location_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function saveRegion($region){
        $data = array(
              'parent_location'  =>0,
              'location_name'  =>strtoupper($region->location_name),
              'country'  =>$region->country,
         );
        $id = (int) $region->location_id;
        if ($id == 0) {
            $this->insert($data);
            $lastInsertId=$this->lastInsertValue;
            return $lastInsertId;
        }else{
            $this->update($data,array('location_id'=>$id));
            return $id;
        }
    }
    
    public function saveDistrict($district){
        $row  = $this->select(array('location_id' => (int) $district->parent_location))->current();
        $data = array(
              'parent_location'  =>$district->parent_location,
              'location_name'  =>strtoupper($district->location_name),
              'country'  =>$row->country,
         );
        $id = (int) $district->location_id;
        if ($id == 0) {
            $this->insert($data);
            $lastInsertId=$this->lastInsertValue;
            return $lastInsertId;
        }else{
            $this->update($data,array('location_id'=>$id));
            return $id;
        }
    }
    
    public function getLocation($id){
        return $this->select(array('location_id' => (int) $id))->current();
    }
}