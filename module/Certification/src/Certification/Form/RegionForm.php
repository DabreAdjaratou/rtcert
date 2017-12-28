<?php

namespace Certification\Form;

 use Zend\Form\Form;

 class RegionForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('region');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'region_name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Name of Region',
             ),
         ));
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),));
        
     }
 }

