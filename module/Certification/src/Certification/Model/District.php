<?php
 namespace Certification\Model;
 use Zend\InputFilter\InputFilter;
 use Zend\InputFilter\InputFilterAwareInterface;
 use Zend\InputFilter\InputFilterInterface;

 class District
 {
     public $location_id;
     public $parent_location;
     public $location_name;
     protected $inputFilter;

     public function exchangeArray($data)
     {
         $this->location_id     = (!empty($data['location_id'])) ? $data['location_id'] : null;
         $this->parent_location  = (!empty($data['parent_location'])) ? $data['parent_location'] : null;
         $this->location_name = (!empty($data['location_name'])) ? $data['location_name'] : null;
     }
      public function getArrayCopy()
     {
         return get_object_vars($this);
     }

          public function setInputFilter(InputFilterInterface $inputFilter)
     {
         throw new \Exception("Not used");
     }

     public function getInputFilter()
     {
         if (!$this->inputFilter) {
             $inputFilter = new InputFilter();

             $inputFilter->add(array(
                 'name'     => 'location_id',
                 'required' => false,
                 'filters'  => array(
                     array('name' => 'Int'),
                 ),
             ));

             $inputFilter->add(array(
                'name' => 'parent_location',
                'required' => true,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

             $inputFilter->add(array(
                 'name'     => 'location_name',
                 'required' => true,
                 'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                 'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => 100,
                         ),
                     ),
                 ),
             ));
             
             $this->inputFilter = $inputFilter;
         }

         return $this->inputFilter;
     }

 }
