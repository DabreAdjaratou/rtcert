<?php
 
 namespace Certification\Model;

 use Zend\Db\TableGateway\TableGateway;

 class FacilityTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'facility_name', 'facility_address', 'district'));
        $sqlSelect->join('certification_districts', 'certification_districts.id= certification_facilities.district ', array('district_name'), 'left');
        $sqlSelect->order('facility_name asc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
     }

     public function getFacility($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveFacility(Facility $facility)
     {
         $data = array(
             'facility_name' =>strtoupper( $facility->facility_name),
             'facility_address'  => $facility->facility_address,
             'district'  => $facility->district,
         );

         $id = (int) $facility->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getFacility($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Facility id does not exist');
             }
         }
     }
     
     public function deleteFacility($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
     
     public function foreigne_key($facility){
         $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT COUNT(facility_id) as nombre from provider  WHERE facility_id='.$facility;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;

     }

    
 }

