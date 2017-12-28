<?php

namespace Certification\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class WrittenExamForm extends Form {

    protected $adapter;

    public function __construct(AdapterInterface $dbAdapter) {

        $this->adapter = $dbAdapter;

        parent::__construct("written_exam");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id_written_exam',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'exam_type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Number of Attempts',
                'disable_inarray_validator' => true, 
                           ),
        ));
        $this->add(array(
            'name' => 'provider_id',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Tester',
                'empty_option' => 'Please choose a Tester',
                'value_options' => $this->getListProvider(),
            ),
        ));

        $this->add(array(
            'name' => 'exam_admin',
            'type' => 'text',
            'options' => array(
                'label' => 'Exam administered by',
            ),
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Text',
            'attributes' => [
                'id' => 'date',
                'type' => 'text',
            ],
            'options' => array(
                'label' => 'Date',
            ),
        ));

        $this->add(array(
            'name' => 'qa_point',
            'type' => 'text',
            'options' => array(
                'label' => '1.QA (points)',
            )
        ));
        $this->add(array(
            'name' => 'rt_point',
            'type' => 'text',
            'options' => array(
                'label' => '2.RT (points)',
            )
        ));
        $this->add(array(
            'name' => 'safety_point',
            'type' => 'text',
            'options' => array(
                'label' => '3.Safety (points)',
            ),
        ));

        $this->add(array(
            'name' => 'specimen_point',
            'type' => 'text',
            'options' => array(
                'label' => '4.Specimen collection (points)',
            ),
        ));

        $this->add(array(
            'name' => 'testing_algo_point',
            'type' => 'text',
            'options' => array(
                'label' => '5.Testing algorithm (points)',
            ),));
        $this->add(array(
            'name' => 'report_keeping_point',
            'type' => 'text',
            'options' => array(
                'label' => '6.Record keeping (points)',
            ),
        ));
        $this->add(array(
            'name' => 'EQA_PT_points',
            'type' => 'text',
            'options' => array(
                'label' => '7. EQA/PT (points)',
            ),
        ));

        $this->add(array(
            'name' => 'ethics_point',
            'type' => 'text',
            'options' => array(
                'label' => '8.Ethics (points)',
            ),
        ));

        $this->add(array(
            'name' => 'inventory_point',
            'type' => 'text',
            'options' => array(
                'label' => '9.Inventory (points)',
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

        $this->add(array(
            'name' => 'practical',
            'type' => 'hidden',
        ));
    }

    public function getListProvider() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT id,certification_id,last_name,first_name,middle_name FROM provider order by last_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['id']] = $res['last_name'] . ' ' . $res['first_name'] . ' ' . $res['middle_name'];
        }
        return $selectData;
    }

   

}
