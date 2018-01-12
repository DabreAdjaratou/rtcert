<?php

namespace Certification\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;

class DistrictForm extends Form {

    public function __construct(AdapterInterface $dbAdapter) {
        $this->adapter = $dbAdapter;
        // we want to ignore the name passed
        parent::__construct('district');

        $this->add(array(
            'name' => 'location_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'parent_location',
            'type' => 'select',
            'options' => array(
                'label' => 'Region',
                'empty_option' => 'Please Choose a Region',
                'value_options' => $this->getRegion(),
            ),
        ));
        $this->add(array(
            'name' => 'location_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name of District',
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

    public function getRegion() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT location_id, location_name FROM location_details WHERE parent_location = 0 ORDER by location_name asc';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['location_id']] = ucwords($res['location_name']);
        }
        return $selectData;
    }

}
