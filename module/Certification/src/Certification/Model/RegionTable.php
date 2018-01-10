<?php
namespace Certification\Model;

 use Zend\Db\TableGateway\TableGateway;

 class RegionTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getRegion($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveRegion(Region $region)
     {
         $data = array(
              'region_name'  =>strtoupper($region->region_name),
         );

         $id = (int) $region->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getRegion($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Region id does not exist');
             }
         }
     }
     
      public function deleteRegion($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
     
     public function foreigne_key($region){
         $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT COUNT(region) as nombre from certification_districts  WHERE region='.$region;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;

     }

    
 }

