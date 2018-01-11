<?php

namespace Certification\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Training {

    public $training_id;
    public $Provider_id;
    public $type_of_competency;
    public $last_training_date;
    public $type_of_training;
    public $length_of_training;
    public $training_organization_id;
    public $facilitator;
    public $training_certificate;
    public $date_certificate_issued;
    public $Comments;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->training_id = (!empty($data['training_id'])) ? $data['training_id'] : null;
        $this->Provider_id = (!empty($data['Provider_id'])) ? $data['Provider_id'] : null;
        $this->type_of_competency = (!empty($data['type_of_competency'])) ? $data['type_of_competency'] : null;
        $this->last_training_date = (!empty($data['last_training_date'])) ? $data['last_training_date'] : null;
        $this->type_of_training = (!empty($data['type_of_training'])) ? $data['type_of_training'] : null;
        $this->training_organization_id = (!empty($data['training_organization_id'])) ? $data['training_organization_id'] : null;
        $this->length_of_training = (!empty($data['length_of_training'])) ? $data['length_of_training'] : null;
        $this->facilitator = (!empty($data['facilitator'])) ? $data['facilitator'] : null;
        $this->date_certificate_issued = (!empty($data['date_certificate_issued'])) ? $data['date_certificate_issued'] : null;
        $this->training_certificate = (!empty($data['training_certificate'])) ? $data['training_certificate'] : null;
        $this->Comments = (!empty($data['Comments'])) ? $data['Comments'] : null;

        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->middle_name = (!empty($data['middle_name'])) ? $data['middle_name'] : null;
        $this->professional_reg_no = (!empty($data['professional_reg_no'])) ? $data['professional_reg_no'] : null;
        $this->certification_id = (!empty($data['certification_id'])) ? $data['certification_id'] : null;
        $this->certification_reg_no = (!empty($data['certification_reg_no'])) ? $data['certification_reg_no'] : null;

        $this->training_organization_name = (!empty($data['training_organization_name'])) ? $data['training_organization_name'] : null;
        $this->type_organization = (!empty($data['type_organization'])) ? $data['type_organization'] : null;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

//
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'training_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'Provider_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'type_of_competency',
                'required' =>true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'last_training_date',
                'required' => true,
            ));

            
            $inputFilter->add(array(
                'name' => 'type_of_training',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'length_of_training',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            
            
            $inputFilter->add(array(
                'name' => 'training_organization_id',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'facilitator',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'date_certificate_issued',
                'required' => FALSE,
            ));

           $inputFilter->add(array(
                'name' => 'training_certificate',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'Comments',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
