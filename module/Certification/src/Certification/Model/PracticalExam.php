<?php

namespace Certification\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class PracticalExam implements InputFilterAwareInterface {

    public $practice_exam_id;
    public $exam_type;
    public $exam_admin;
    public $provider_id;
    public $Sample_testing_score;
    public $direct_observation_score;
    public $practical_total_score;
    public $date;
    protected $inputFilter;

    public function exchangeArray($data) {

        $this->practice_exam_id = (!empty($data['practice_exam_id'])) ? $data['practice_exam_id'] : null;
        $this->exam_type = (!empty($data['exam_type'])) ? $data['exam_type'] : null;
        $this->exam_admin = (!empty($data['exam_admin'])) ? $data['exam_admin'] : null;
        $this->provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : null;
        $this->Sample_testing_score = (!empty($data['Sample_testing_score'])) ? $data['Sample_testing_score'] : null;
        $this->direct_observation_score = (!empty($data['direct_observation_score'])) ? $data['direct_observation_score'] : null;
        $this->practical_total_score = (!empty($data['practical_total_score'])) ? $data['practical_total_score'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : null;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->middle_name = (!empty($data['middle_name'])) ? $data['middle_name'] : null;
        $this->admin_last_name = (!empty($data['admin_last_name'])) ? $data['admin_last_name'] : null;
        $this->admin_first_name = (!empty($data['admin_first_name'])) ? $data['admin_first_name'] : null;
        $this->admin_middle_name = (!empty($data['admin_middle_name'])) ? $data['admin_middle_name'] : null;
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
                'name' => 'practice_exam_id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'exam_type',
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
                'name' => 'exam_admin',
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
                'name' => 'provider_id',
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
                'name' => 'direct_observation_score',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'Sample_testing_score',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'date',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name' => 'written',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
