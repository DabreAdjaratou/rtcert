<?php

namespace Certification\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Certification\Model\Training;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TrainingTable extends AbstractTableGateway {

    private $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('training_id', 'Provider_id', 'type_of_competency', 'last_training_date', 'type_of_training', 'length_of_training', 'training_organization_id', 'facilitator', 'training_certificate', 'date_certificate_issued', 'Comments'));
        $sqlSelect->join('provider', 'provider.id = training.Provider_id', array('last_name', 'first_name', 'middle_name', 'professional_reg_no', 'certification_id', 'certification_reg_no'), 'left')
                ->join('training_organization', 'training_organization.training_organization_id = training.training_organization_id ', array('training_organization_name', 'type_organization'), 'left');
        $sqlSelect->order('training_id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function getTraining($training_id) {
        $training_id = (int) $training_id;
        $rowset = $this->tableGateway->select(array('training_id' => $training_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $training_id");
        }
        return $row;
    }

    public function saveTraining(Training $Training) {
        $date = $Training->last_training_date;
        $date_explode = explode("-", $date);
//        die(print_r($date_explode));
        $newsdate = $date_explode[2] . '-' . $date_explode[1] . '-' . $date_explode[0];

        if (isset($Training->date_certificate_issued)) {
            $date2 = $Training->date_certificate_issued;
            $date_explode2 = explode("-", $date2);
            $newsdate2 = $date_explode2[2] . '-' . $date_explode2[1] . '-' . $date_explode2[0];

            $data = array(
                'Provider_id' => $Training->Provider_id,
                'type_of_competency' => $Training->type_of_competency,
                'last_training_date' => $newsdate,
                'type_of_training' => $Training->type_of_training,
                'length_of_training' => $Training->length_of_training,
                'training_organization_id' => $Training->training_organization_id,
                'facilitator' => strtoupper($Training->facilitator),
                'training_certificate' => $Training->training_certificate,
                'date_certificate_issued' => $newsdate2,
                'Comments' => $Training->Comments,
            );
        } else {
            $data = array(
                'Provider_id' => $Training->Provider_id,
                'type_of_competency' => $Training->type_of_competency,
                'last_training_date' => $newsdate,
                'type_of_training' => $Training->type_of_training,
                'length_of_training' => $Training->length_of_training,
                'training_organization_id' => $Training->training_organization_id,
                'facilitator' => strtoupper($Training->facilitator),
                'training_certificate' => $Training->training_certificate,
                'date_certificate_issued' => $Training->date_certificate_issued,
                'Comments' => $Training->Comments,
            );
        }
        $training_id = (int) $Training->training_id;
        if ($training_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTraining($training_id)) {
                $this->tableGateway->update($data, array('training_id' => $training_id));
            } else {
                throw new \Exception('Training  id does not exist');
            }
        }
    }
    
     public function deleteTraining($training_id)
     {
         $this->tableGateway->delete(array('training_id' => (int) $training_id));
     }

     public function report($type_of_competency,$type_of_training,$training_organization_id,$training_certificate,$typeHiv,$jobTitle,$region,$district,$facility) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT provider.certification_reg_no, provider.certification_id, provider.professional_reg_no, provider.first_name, provider.last_name, provider.middle_name,certification_regions.region_name,certification_districts.district_name, provider.type_vih_test, provider.phone,provider.email, provider.prefered_contact_method,provider.current_jod, provider.time_worked,provider.test_site_in_charge_name, provider.test_site_in_charge_phone,provider.test_site_in_charge_email, provider.facility_in_charge_name, provider.facility_in_charge_phone, provider.facility_in_charge_email,certification_facilities.facility_name, type_of_competency, last_training_date, type_of_training, length_of_training, facilitator, training_certificate, date_certificate_issued, Comments, training_organization_name, type_organization FROM provider,training, certification_regions,certification_districts,certification_facilities, training_organization where provider.id=training.Provider_id and provider.region=certification_regions.id and provider.district=certification_districts.id and provider.facility_id=certification_facilities.id and training.training_organization_id=training_organization.training_organization_id';
       
        if (!empty($type_of_competency)) {
            $sql = $sql . ' and type_of_competency="' . $type_of_competency . '"';
        }
        
         if (!empty($type_of_training)) {
            $sql = $sql . ' and type_of_training="' . $type_of_training . '"';
        }
        
         if (!empty($training_organization_id)) {
            $sql = $sql . ' and training_organization.training_organization_id=' . $training_organization_id;
        }
        
         if (!empty($training_certificate)) {
            $sql = $sql . ' and training_certificate="' . $training_certificate.'"';
        }

        if (!empty($typeHiv)) {
            $sql = $sql . ' and provider.type_vih_test="' . $typeHiv . '"';
        }
        if (!empty($jobTitle)) {
            $sql = $sql . ' and provider.current_jod="' . $jobTitle . '"';
        }

        if (!empty($region)) {
            $sql = $sql . ' and certification_regions.id=' . $region;
        }

        if (!empty($district)) {
            $sql = $sql . ' and certification_districts.id=' . $district;
        }

        if (!empty($facility)) {
            $sql = $sql . ' and certification_facilities.id=' . $facility;
        }
//        die($sql);

        $statement = $db->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
    public function getRegions() {
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT id, region_name FROM certification_regions  ORDER by region_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['region_name'];
        }
//        die(print_r($selectData));
        return $selectData;
    }
}
