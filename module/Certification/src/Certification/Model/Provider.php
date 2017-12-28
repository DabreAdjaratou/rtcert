<?php

namespace Certification\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class Provider {

    public $id;
    public $certification_reg_no;
    public $certification_id;
    public $professional_reg_no;
    public $last_name;
    public $first_name;
    public $middle_name;
    public $region;
    public $district;
    public $type_vih_test;
    public $phone;
    public $email;
    public $prefered_contact_method;
    public $current_jod;
    public $time_worked;
    public $test_site_in_charge_name;
    public $test_site_in_charge_phone;
    public $test_site_in_charge_email;
    public $facility_in_charge_name;
    public $facility_in_charge_phone;
    public $facility_in_charge_email;
    public $facility_id;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->certification_reg_no = (!empty($data['certification_reg_no'])) ? $data['certification_reg_no'] : null;
        $this->professional_reg_no = (!empty($data['professional_reg_no'])) ? $data['professional_reg_no'] : null;
        $this->certification_id = (!empty($data['certification_id'])) ? $data['certification_id'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->middle_name = (!empty($data['middle_name'])) ? $data['middle_name'] : null;
        $this->region = (!empty($data['region'])) ? $data['region'] : null;
        $this->district = (!empty($data['district'])) ? $data['district'] : null;
        $this->type_vih_test = (!empty($data['type_vih_test'])) ? $data['type_vih_test'] : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->email = (!empty($data['email'])) ? $data['email'] : null;
        $this->prefered_contact_method = (!empty($data['prefered_contact_method'])) ? $data['prefered_contact_method'] : null;
        $this->current_jod = (!empty($data['current_jod'])) ? $data['current_jod'] : null;
        $this->time_worked = (!empty($data['time_worked'])) ? $data['time_worked'] : null;
        $this->test_site_in_charge_name = (!empty($data['test_site_in_charge_name'])) ? $data['test_site_in_charge_name'] : null;
        $this->test_site_in_charge_phone = (!empty($data['test_site_in_charge_phone'])) ? $data['test_site_in_charge_phone'] : null;
        $this->test_site_in_charge_email = (!empty($data['test_site_in_charge_email'])) ? $data['test_site_in_charge_email'] : null;
        $this->facility_in_charge_name = (!empty($data['facility_in_charge_name'])) ? $data['facility_in_charge_name'] : null;
        $this->facility_in_charge_phone = (!empty($data['facility_in_charge_phone'])) ? $data['facility_in_charge_phone'] : null;
        $this->facility_in_charge_email = (!empty($data['facility_in_charge_email'])) ? $data['facility_in_charge_email'] : null;
        $this->facility_id = (!empty($data['facility_id'])) ? $data['facility_id'] : null;
        $this->facility_name = (!empty($data['facility_name'])) ? $data['facility_name'] : null;
        $this->facility_address = (!empty($data['facility_address'])) ? $data['facility_address'] : null;
    
        $this->region_name = (!empty($data['region_name'])) ? $data['region_name'] : null;
        $this->district_name = (!empty($data['district_name'])) ? $data['district_name'] : null;
        
        
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'certification_reg_no',
                'required' => false,
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
                'name' => 'certification_id',
                'required' => false,
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
                'name' => 'professional_reg_no',
                'required' => false,
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
                'name' => 'last_name',
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
                'name' => 'first_name',
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
                'name' => 'middle_name',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array('encoding' => 'UTF-8',),
                    ),
                ),
            ));


            $inputFilter->add(array(
                'name' => 'region',
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
                'name' => 'district',
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
                'name' => 'type_vih_test',
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
                'name' => 'phone',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'email',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'prefered_contact_method',
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
                'name' => 'current_jod',
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
                'name' => 'time_worked',
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
                'name' => 'test_site_in_charge_name',
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
                'name' => 'test_site_in_charge_phone',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'test_site_in_charge_email',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'facility_in_charge_name',
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
                'name' => 'facility_in_charge_phone',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'facility_in_charge_email',
                'required' => false,
            ));

            $inputFilter->add(array(
                'name' => 'facility_id',
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

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
