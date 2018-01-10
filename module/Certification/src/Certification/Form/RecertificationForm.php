<?php

namespace Certification\Form;

use Zend\Form\Form;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class RecertificationForm extends Form {

    public function __construct(AdapterInterface $dbAdapter) {
        
        $this->adapter = $dbAdapter;
        // we want to ignore the name passed
        parent::__construct('recertification');

        $this->add(array(
            'name' => 'recertification_id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'due_date',
            'type' => 'Text',
            'attributes' => [
                'id' => 'date',
                'type' => 'text'
            ],
            'options' => array(
                'label' => 'Due Date'
            )
        ));
        $this->add(array(
        'name' => 'provider_id',
        'type' => 'Zend\Form\Element\Select',
        'options' => array(
        'label' => 'Tester',
        'empty_option' => 'Please choose a tester',
        'value_options' => $this->getProvider()
        ),));
        $this->add(array(
            'name' => 'reminder_type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type of Reminder',
                'empty_option' => 'Please choose a Type',
                'value_options' => array(
                    'Phone' => 'Phone',
                    'Email' => 'Email'
                ),
            ),
        ));
        $this->add(array(
            'name' => 'reminder_sent_to',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Reminder Sent To',
                'empty_option' => 'Please choose a Reminder',
                'value_options' => array(
                    'District focal person' => 'District focal person',
                    'HTS focal person' => 'HTS focal person',
                    'Implementing partner' => 'Implementing partner',
                    'Facility in charge' => 'Facility in charge',
                    'Provider' => 'Provider',
                    'QA/QI focal person' => 'QA/QI focal person'
                ),
            ),
        ));
        $this->add(array(
            'name' => 'name_of_recipient',
            'type' => 'Text',
            'options' => array(
                'label' => 'Name of Recipient',
            ),
        ));
        $this->add(array(
            'name' => 'date_reminder_sent',
            'type' => 'Text',
            'attributes' => [
                'id' => 'date',
                'type' => 'text'
            ],
            'options' => array(
                'label' => 'Date Reminder Sent'
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
    }
    
     public function getProvider(){
        $dbAdapter = $this->adapter;
//        $sql = 'select id, last_name, first_name, middle_name from provider ';
        $sql='select id, last_name, first_name, middle_name from provider where certification_id is not null';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData=[];
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['last_name'].' '.$res['first_name'].' '.$res['middle_name'];
        }
        return $selectData;
    }

}
