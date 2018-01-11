<?php

namespace Certification\Form;

use Zend\Session\Container;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Form\Form;

class ProviderForm extends Form {

    protected $adapter;

    public function __construct(AdapterInterface $dbAdapter) {

        $this->adapter = $dbAdapter;
        
        parent::__construct("provider");
       
        $this->setAttributes(array('method' => 'post',
        ));
       
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'certification_reg_no',
            'type' => 'Text',
            'options' => array(
                'label' => 'Certification Registration ID',
            ),
        ));

        $this->add(array(
            'name' => 'certification_id',
            'type' => 'Text',
            'options' => array(
                'label' => 'Certification ID',
            ),
        ));

        $this->add(array(
            'name' => 'professional_reg_no',
            'type' => 'Text',
            'options' => array(
                'label' => 'Professional Registration No (if available)',
            ),
        ));
        $this->add(array(
            'name' => 'last_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Last Name (Surname)',
            ),
        ));
        $this->add(array(
            'name' => 'first_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'First Name',
            ),
        ));

        $this->add(array(
            'name' => 'middle_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Middle Name (3rd name)',
            ),
        ));
        
         $this->add(array(
            'name' => 'country',
            'type' => 'select',
            'options' => array(
                'label' => 'Country',
                'disable_inarray_validator' => true,
                'empty_option' => 'Please Choose a Country',
                'value_options' => $this->getAllActiveCountries(),
            ),
        ));
         
        $this->add(array(
            'name' => 'region',
            'type' => 'select',
            'options' => array(
                'label' => 'Region',
                'disable_inarray_validator' => true,
                'empty_option' => 'Please Choose a Country First',
                //'value_options' => $this->getAllRegions(),
            ),
        ));
        $this->add(array(
            'name' => 'district',
            'type' => 'Select',
            'options' => array(
                'label' => 'District',
                'disable_inarray_validator' => true,
                'empty_option' => 'Please Choose a Region First',
                //'value_options' => $this->getAllDistricts(),
            ),
        ));

        $this->add(array(
            'name' => 'type_vih_test',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type HIV Test Modality/Point',
                'empty_option' => 'Please Choose a Type',
                'value_options' => array(
                    'ART clinic' => 'ART clinic',
                    'Community' => 'Community',
                    'IPD' => 'IPD',
                    'LAB' => 'LAB',
                    'OPD' => 'OPD',
                    'PITC' => 'PITC',
                    'PMTCT' => 'PMTCT',
                    'STI clinic' => 'STI clinic',
                    'TB clinic' => 'TB clinic',
                    'VCT/HTC' => 'VCT/HTC',
                )
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'text',
            'options' => array(
                'label' => 'Phone',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'email',
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'prefered_contact_method',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Prefered Contact Method',
                'empty_option' => 'Please Choose a Method',
                'value_options' => array(
                    'Phone' => 'Phone',
                    'Email' => 'Email'
                )
            ),
        ));
        $this->add(array(
            'name' => 'current_jod',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Current Job Title',
                'empty_option' => 'Please Choose a Job Title',
                'value_options' => array(
                    'Assistant Medical Officer' => 'Assistant Medical Officer',
                    'Counselor' => 'Counselor',
                    'Health assistant' => 'Health assistant',
                    'Health attendant' => 'Health attendant',
                    'Lab Assistant' => 'Lab Assistant',
                    'Lab Scientist' => 'Lab Scientist',
                    'Lab technician' => 'Lab technician',
                    'Lab technologist' => 'Lab technologist',
                    'Medical doctor' => 'Medical doctor',
                    'Midwife' => 'Midwife',
                    'Nurse' => 'Nurse',
                    'Nurse assistant' => 'Nurse assistant'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'time_worked',
            'type' => 'Number',
            'options' => array(
                'label' => 'Time Worked As Tester',
            ),
            'attributes' => [
                'min' => '1',
                'step' => '1', // default step interval is 1
            ],
        ));

        $this->add(array(
            'name' => 'test_site_in_charge_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Full Name',
            ),
        ));
        $this->add(array(
            'name' => 'test_site_in_charge_phone',
            'type' => 'Text',
            'options' => array(
                'label' => 'Phone',
            ),
        ));
        $this->add(array(
            'name' => 'test_site_in_charge_email',
            'type' => 'email',
            'options' => array(
                'label' => 'Email',
            ),
        ));
        $this->add(array(
            'name' => 'facility_in_charge_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Full Name',
            ),
        ));
        $this->add(array(
            'name' => 'facility_in_charge_phone',
            'type' => 'Text',
            'options' => array(
                'label' => 'Phone',
            ),
        ));
        $this->add(array(
            'name' => 'facility_in_charge_email',
            'type' => 'email',
            'options' => array(
                'label' => 'Email',
            ),
        ));



        $this->add(array(
            'name' => 'facility_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Facility Name',
                'disable_inarray_validator' => true,
                'empty_option' => 'Please Choose a District first',
            ),
        ));        

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }

    public function getRegions() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT id, region_name FROM certification_regions  ORDER by region_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['region_name'];
        }
        return $selectData;
    }
    
    public function getAllActiveCountries() {
        $logincontainer = new Container('credo');
        $countryWhere = 'WHERE country_status = "active"';
        if(isset($logincontainer->country) && count($logincontainer->country) > 0){
            $countryWhere = 'WHERE country_id IN('.implode(',',$logincontainer->country).') AND country_status = "active"';
        }
        $dbAdapter = $this->adapter;
        $sql = 'SELECT country_id, country_name FROM country '.$countryWhere.' ORDER by country_name asc';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['country_id']] = $res['country_name'];
        }
        return $selectData;
    }
    
    public function getAllRegions(){
        $dbAdapter = $this->adapter;
        $sql = 'SELECT location_id, location_name FROM location_details WHERE parent_location = 0 ORDER by location_name asc';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['location_id']] = $res['location_name'];
        }
        return $selectData;
    }
    
    public function getAllDistricts(){
        $dbAdapter = $this->adapter;
        $sql = 'SELECT location_id, location_name FROM location_details WHERE parent_location != 0 ORDER by location_name asc';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['location_id']] = $res['location_name'];
        }
        return $selectData;
    }

}
