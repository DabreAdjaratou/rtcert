<?php
namespace Certification\Form;
 use Zend\Form\Form;

 class CertificationMailForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('certification-mail');

         $this->add(array(
             'name' => 'mail_id',
             'type' => 'Hidden',
         ));
          $this->add(array(
             'name' => 'type',
             'type' => 'select',
             'options' => array(
                'label' => 'Type Of Email',
//                'empty_option' => 'Please Choose An Option',
                'value_options' => array(
                    '1'=>'Send Certificate',
                    '2'=>'Send Reminder')
             ),
         ));
         
         $this->add(array(
             'name' => 'to_email',
             'type' => 'Zend\Form\Element\Email',
             'options' => array(
                 'label' => 'To ',
             ),
         ));
         $this->add(array(
             'name' => 'cc',
             'type' => 'Zend\Form\Element\Email',
             'options' => array(
                 'label' => 'cc',
             ),
         ));
         $this->add(array(
             'name' => 'bcc',
             'type' => 'Zend\Form\Element\Email',
             'options' => array(
                 'label' => 'bcc',
             ),
         ));
         $this->add(array(
             'name' => 'subject',
             'type' => 'textarea',
             'options' => array(
                 'label' => 'Subject'
                 ),
         ));
         
         $this->add(array(
             'name' => 'message',
             'type' => 'textarea',
             'options' => array(
                 'label' => 'Message',
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
 }
