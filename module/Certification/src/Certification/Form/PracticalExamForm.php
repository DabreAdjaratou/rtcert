<?php

namespace Certification\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class PracticalExamForm extends Form {

    protected $adapter;

    public function __construct(AdapterInterface $dbAdapter) {

        $this->adapter = $dbAdapter;

        parent::__construct("practical_exam");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'practice_exam_id',
            'type' => 'Hidden',
        ));

        $this->add(array(
            'name' => 'exam_type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Number of Attempts',
                 'disable_inarray_validator' => true, 
//                
            ),
        ));

        $this->add(array(
            'name' => 'exam_admin',
            'type' => 'text',
            'options' => array(
                'label' => 'Exam Administrator',
                ),
            ));

        $this->add(array(
            'name' => 'provider_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Tester',
                'empty_option' => 'Please choose a Tester',
                'value_options' => $this->getListProvider()
            ),
        ));
        $this->add(array(
            'name' => 'direct_observation_score',
            'type' => 'text',
            'options' => array(
                'label' => 'Direct Observation Score ( % )',
            ),
            
        ));
        
        $this->add(array(
            'name' => 'Sample_testing_score',
            'type' => 'text',
            'options' => array(
                'label' => 'Sample Testing Score  ( % )',
            ),
            
        ));
        $this->add(array(
            'type' => 'text',
            'name' => 'date',
            'attributes' => [
                'id' => 'date',
                'type' => 'text'
            ],
            'options' => array(
                'label' => 'Date'
            )
        ));


        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));


        $this->add(array(
            'name' => 'written',
            'type' => 'hidden',
        ));
    }

    public function getListProvider() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT id, certification_id,last_name,first_name,middle_name FROM provider order by last_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['id']] = $res['last_name'] . ' ' . $res['first_name'] . ' ' . $res['middle_name'];
        }
        return $selectData;
    }

    
}
