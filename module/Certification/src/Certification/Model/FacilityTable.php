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

     public function fetchAll(){
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT * FROM certification_facilities as c_f LEFT JOIN location_details as l_d ON l_d.location_id=c_f.district ORDER BY facility_name ASC';
        $statement = $db->query($sql);
        $result = $statement->execute();
       return $result;
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

     public function saveFacility(Facility $facility){
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
     
     public function deleteFacility($id){
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

