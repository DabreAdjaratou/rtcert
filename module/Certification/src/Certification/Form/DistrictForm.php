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
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'district_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name of District',
            ),
        ));
        $this->add(array(
            'name' => 'region',
            'type' => 'select',
            'options' => array(
                'label' => 'Region',
                'empty_option' => 'Please Choose A District',
                'value_options' => $this->getRegion(),
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
        $sql = 'SELECT id, region_name FROM certification_regions  ORDER by region_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['region_name'];
        }
        return $selectData;
    }

}
