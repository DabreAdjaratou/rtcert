<?php

namespace Certification\Model;

use Zend\Session\Container;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;

class ProviderTable extends AbstractTableGateway {

    private $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $logincontainer = new Container('credo');
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'certification_reg_no', 'certification_id', 'professional_reg_no', 'last_name', 'first_name', 'middle_name', 'region', 'district', 'type_vih_test', 'phone', 'email', 'prefered_contact_method', 'current_jod', 'time_worked', 'test_site_in_charge_name', 'test_site_in_charge_phone', 'test_site_in_charge_email', 'facility_in_charge_name', 'facility_in_charge_phone', 'facility_in_charge_email', 'facility_id'));
        $sqlSelect->join('certification_facilities', ' certification_facilities.id = provider.facility_id ', array('facility_name', 'facility_address'), 'left')
                  ->join(array('l_d_r'=>'location_details'), 'l_d_r.location_id = provider.region', array('region_name'=>'location_name'), 'left')
                  ->join(array('l_d_d'=>'location_details'), 'l_d_d.location_id = provider.district', array('district_name'=>'location_name'), 'left');
        $sqlSelect->order('certification_reg_no desc');
        if(isset($logincontainer->district) && count($logincontainer->district) > 0){
            $sqlSelect->where('provider.district IN('.implode(',',$logincontainer->district).')');
        }
        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function getProvider($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveProvider(\Certification\Model\Provider $provider) {
        $last_name = strtoupper($provider->last_name);
        $first_name = strtoupper($provider->first_name);
        $middle_name = strtoupper($provider->middle_name);
        $region = ucfirst($provider->region);
        $district = ucfirst($provider->district);
        $test_site_in_charge_name = strtoupper($provider->test_site_in_charge_name);
        $facility_in_charge_name = strtoupper($provider->facility_in_charge_name);

        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT MAX(certification_reg_no) as max FROM provider';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $max = $res['max'];
        }
        $array = explode("R", $max);
        $array2 = explode("-", $max);

        if (date('Y') > $array2[0]) {

            $certification_reg_no = date('Y') . '-R' . substr_replace("0000", 1, -strlen(1));
        } else {

            $certification_reg_no = $array2[0] . '-R' . substr_replace("0000", ($array[1] + 1), -strlen(($array[1] + 1)));
        }


        $data = array(
            'certification_reg_no' => $certification_reg_no,
            'professional_reg_no' => $provider->professional_reg_no,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'region' => $region,
            'district' => $district,
            'type_vih_test' => $provider->type_vih_test,
            'phone' => $provider->phone,
            'email' => $provider->email,
            'prefered_contact_method' => $provider->prefered_contact_method,
            'current_jod' => $provider->current_jod,
            'time_worked' => $provider->time_worked,
            'test_site_in_charge_name' => $test_site_in_charge_name,
            'test_site_in_charge_phone' => $provider->test_site_in_charge_phone,
            'test_site_in_charge_email' => $provider->test_site_in_charge_email,
            'facility_in_charge_name' => $facility_in_charge_name,
            'facility_in_charge_phone' => $provider->facility_in_charge_phone,
            'facility_in_charge_email' => $provider->facility_in_charge_email,
            'facility_id' => $provider->facility_id,
        );
        $data2 = array(
//            'certification_reg_no' => $certification_reg_no,
            'professional_reg_no' => $provider->professional_reg_no,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'region' => $region,
            'district' => $district,
            'type_vih_test' => $provider->type_vih_test,
            'phone' => $provider->phone,
            'email' => $provider->email,
            'prefered_contact_method' => $provider->prefered_contact_method,
            'current_jod' => $provider->current_jod,
            'time_worked' => $provider->time_worked,
            'test_site_in_charge_name' => $test_site_in_charge_name,
            'test_site_in_charge_phone' => $provider->test_site_in_charge_phone,
            'test_site_in_charge_email' => $provider->test_site_in_charge_email,
            'facility_in_charge_name' => $facility_in_charge_name,
            'facility_in_charge_phone' => $provider->facility_in_charge_phone,
            'facility_in_charge_email' => $provider->facility_in_charge_email,
            'facility_id' => $provider->facility_id,
        );

//        print_r($data);
        $id = (int) $provider->id;
        $certification_id = $provider->certification_id;

        if ($id == 0 && !$certification_id) {

            $this->tableGateway->insert($data);
        } else {
            if ($this->getProvider($id)) {
                $data['certification_id'] = $provider->certification_id;
                $this->tableGateway->update($data2, array('id' => $id));
            } else {
                throw new \Exception('Provider id does not exist');
            }
        }
    }

    /**
     * to get facilities list
     * @param type $q (district id)
     * @return type array
     */
     public function getRegion($params) {
        $logincontainer = new Container('credo');
        $regionWhere = '';
        if(isset($logincontainer->region) && count($logincontainer->region) > 0){
            $regionWhere = ' AND location_id IN('.implode(',',$logincontainer->region).')';
        }
        $db = $this->tableGateway->getAdapter();
        $sql = "SELECT location_id, location_name FROM location_details WHERE parent_location = 0 AND country ='" . $params['q'] . "'".$regionWhere;
        $statement = $db->query($sql);
        $result = $statement->execute();
        return $result;
    }
    
     public function getDistrict($q) {
        $logincontainer = new Container('credo');
        $districtWhere = '';
        if(isset($logincontainer->district) && count($logincontainer->district) > 0){
            $districtWhere = ' AND location_id IN('.implode(',',$logincontainer->district).')';
        }
        $db = $this->tableGateway->getAdapter();
        $sql = "SELECT location_id, location_name FROM location_details WHERE parent_location = '" . $q . "'".$districtWhere;
        $statement = $db->query($sql);
        $result = $statement->execute();
//        print_r($result);
        return $result;
    }
    
    public function getFacility($q) {
        $db = $this->tableGateway->getAdapter();
        $sql = "SELECT id, facility_name, district FROM certification_facilities where district='" . $q . "'";
        $statement = $db->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getCountryIdbyRegion($location) {
        $db = $this->tableGateway->getAdapter();
        $sql = "SELECT country FROM location_details WHERE location_id ='" . $location . "'";
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $country_id = $res['country'];
        }
//       die(print_r($id));
        return array('country_id' => $country_id);
    }

    public function getAllActiveCountries(){
        $logincontainer = new Container('credo');
        $countryWhere = 'WHERE country_status = "active"';
        if(isset($logincontainer->country) && count($logincontainer->country) > 0){
            $countryWhere = 'WHERE country_id IN('.implode(',',$logincontainer->country).') AND country_status = "active"';
        }
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT country_id, country_name FROM country '.$countryWhere.' ORDER by country_name asc';
        $statement = $db->query($sql);
        $countryResult = $statement->execute();
        return $countryResult;
    }
    
    public function deleteProvider($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function foreigne_key($provider_id) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT COUNT(provider_id) as nombre from written_exam  WHERE provider_id=' . $provider_id;
        $sql2 = 'SELECT COUNT(provider_id) as nombre from practical_exam  WHERE provider_id=' . $provider_id;
        $sql3 = 'SELECT COUNT(provider_id) as nombre from training  WHERE provider_id=' . $provider_id;
        $statement = $db->query($sql);
        $statement2 = $db->query($sql2);
        $statement3 = $db->query($sql3);

        $result = $statement->execute();
        $result2 = $statement2->execute();
        $result3 = $statement3->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        foreach ($result2 as $res2) {
            $nombre2 = $res2['nombre'];
        }
        foreach ($result3 as $res3) {
            $nombre3 = $res3['nombre'];
        }
        return array(
            'nombre' => $nombre,
            'nombre2' => $nombre2,
            'nombre3' => $nombre3
        );
    }

    public function report($typeHiv, $jobTitle, $region, $district, $facility, $contact_method) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'select provider.certification_reg_no, provider.certification_id, provider.professional_reg_no, provider.first_name, provider.last_name, provider.middle_name,certification_regions.region_name,certification_districts.district_name, provider.type_vih_test, provider.phone,provider.email, provider.prefered_contact_method,provider.current_jod, provider.time_worked,provider.test_site_in_charge_name, provider.test_site_in_charge_phone,provider.test_site_in_charge_email, provider.facility_in_charge_name, provider.facility_in_charge_phone, provider.facility_in_charge_email,certification_facilities.facility_name from provider,certification_districts, certification_facilities, certification_regions WHERE provider.facility_id=certification_facilities.id and provider.region= certification_regions.id  and provider.district=certification_districts.id';

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

        if (!empty($contact_method)) {
            $sql = $sql . ' and prefered_contact_method="' . $contact_method . '"';
        }


//die($sql);
        $statement = $db->query($sql);
        $result = $statement->execute();
        return $result;
    }

}
