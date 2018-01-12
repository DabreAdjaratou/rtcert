<?php

namespace Certification\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class FacilityForm extends Form {

    public function __construct(AdapterInterface $dbAdapter) {
        $this->adapter = $dbAdapter;
        // we want to ignore the name passed
        parent::__construct('certification_facilities');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'district',
            'type' => 'select',
            'options' => array(
                'label' => 'District',
                'empty_option' => 'Please Choose a District',
                'value_options' => $this->getDistrict(),
            ),
        ));
        $this->add(array(
            'name' => 'facility_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name of Facility',
            ),
        ));
        $this->add(array(
            'name' => 'facility_address',
            'type' => 'Text',
            'options' => array(
                'label' => 'Facility Address',
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

    public function getDistrict() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT location_id, location_name FROM location_details WHERE parent_location != 0 ORDER by location_name asc';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['location_id']] = ucwords($res['location_name']);
        }
        return $selectData;
    }

}
