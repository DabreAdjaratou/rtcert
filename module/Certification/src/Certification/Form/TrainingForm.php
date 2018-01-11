<?php

namespace Certification\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class TrainingForm extends Form {

    protected $adapter;

    public function __construct(AdapterInterface $dbAdapter) {

        $this->adapter = $dbAdapter;

        parent::__construct("training");
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'training_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'Provider_id',
            'options' => array(
                'label' => 'Tester',
                'empty_option' => 'Please choose a Provider',
                'value_options' => $this->getListProvider(),
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type_of_competency',
            'options' => array(
                'label' => 'Type of Competency',
                'empty_option' => 'Please choose a Type of Competency',
                'value_options' => array(
                    'Initial' => 'Initial',
                    'Maintenance' => 'Maintenance',
                ),
            ),
        ));


        $this->add(array(
            'type' => 'text',
            'name' => 'last_training_date',
            'attributes' => [
                'id' => 'date',
                'type' => 'text'
            ],
            'options' => array(
                'label' => 'Date of Last Training/Activity'
            )
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type_of_training',
            'options' => array(
                'label' => 'Type of Activity/Training',
                'empty_option' => 'Please choose a Type of Training',
                'value_options' => array(
                    'Initial training (Nationally approved RT training)' => 'Initial  training (Nationally approved RT training)',
                    'Initial  training (on the job training by peer)' => 'Initial  training (on the job training by peer)',
                    'In-service training (on job by supervisor)' => 'In-service training (on job by supervisor)',
                    'In-service training (nationally approved)' => 'In-service training (nationally approved)',
                    'In-service training (distance learning, i.e., ECHO)' => 'In-service training (distance learning, i.e., ECHO)',
                    'Mentoring (on job by supervisor)' => 'Mentoring (on job by supervisor)',
                    'Mentoring (Distance learning, i.e., ECHO)' => 'Mentoring (Distance learning, i.e., ECHO',
                    'Refresher training (Nationally approved RT training)' => 'Refresher training (Nationally approved RT training)',
                    'Refresher training (Topic specific training)' => 'Refresher training (Topic specific training)',
                    'Regional workshop' => 'Regional workshop',
                ),
            ),
        ));

        $this->add(array(
            'type' => 'Number',
            'name' => 'length_of_training',
            'options' => array(
                'label' => 'Length of Activity/Training',
            ),
            'attributes' => [
                'min' => '1',
                'step' => '1', // default step interval is 1
            ],
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'training_organization_id',
            'options' => array(
                'label' => 'Training Organization',
                'empty_option' => 'Please choose an organization',
                'value_options' => $this->getListTrainingOrganization(),
            ),
        ));

        $this->add(array(
            'type' => 'text',
            'name' => 'facilitator',
            'options' => array(
                'label' => 'Name of Facilitator(s)',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'training_certificate',
            'options' => array(
                'label' => 'Training certificate (if available)',
                'empty_option' => 'Please choose a training certificate',
                'value_options' => array(
                    'Yes' => 'Yes',
                    'No' => 'No',
                ),
            ),
        ));



        $this->add(array(
            'type' => 'text',
            'name' => 'date_certificate_issued',
            'attributes' => [
                'id' => 'date2',
                'type' => 'text'
            ],
            'options' => array(
                'label' => 'Date certificate issued (if available)'
            )
        ));

        $this->add(array(
            'name' => 'Comments',
            'attributes' => array(
                'type' => 'textarea'
            ),
            'options' => array(
                'label' => 'Comments',
            ),
        ));



        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Add',
                'id' => 'submitbutton',
            ),
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

    public function getListTrainingOrganization() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT training_organization_id,training_organization_name FROM training_organization order by training_organization_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['training_organization_id']] = $res['training_organization_name'];
        }
        return $selectData;
    }

}
