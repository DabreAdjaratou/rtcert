<?php

namespace Certification\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class WrittenExam implements InputFilterAwareInterface {

    public $id_written_exam;
    public $exam_type;
    public $provider_id;
    public $exam_admin;
    public $date;
    public $qa_point;
    public $rt_point;
    public $safety_point;
    public $specimen_point;
    public $testing_algo_point;
    public $report_keeping_point;
    public $EQA_PT_points;
    public $ethics_point;
    public $inventory_point;
    public $total_points;
    public $final_score;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->id_written_exam = (!empty($data['id_written_exam'])) ? $data['id_written_exam'] : null;
        $this->exam_type = (!empty($data['exam_type'])) ? $data['exam_type'] : null;
        $this->provider_id = (!empty($data['provider_id'])) ? $data['provider_id'] : null;
        $this->exam_admin = (!empty($data['exam_admin'])) ? $data['exam_admin'] : null;
        $this->date = (!empty($data['date'])) ? $data['date'] : null;
        $this->qa_point = (!empty($data['qa_point'])) ? $data['qa_point'] : 0;
        $this->rt_point = (!empty($data['rt_point'])) ? $data['rt_point'] : 0;
        $this->safety_point = (!empty($data['safety_point'])) ? $data['safety_point'] : 0;
        $this->specimen_point = (!empty($data['specimen_point'])) ? $data['specimen_point'] : 0;
        $this->testing_algo_point = (!empty($data['testing_algo_point'])) ? $data['testing_algo_point'] : 0;
        $this->report_keeping_point = (!empty($data['report_keeping_point'])) ? $data['report_keeping_point'] : 0;
        $this->EQA_PT_points = (!empty($data['EQA_PT_points'])) ? $data['EQA_PT_points'] : 0;
        $this->ethics_point = (!empty($data['ethics_point'])) ? $data['ethics_point'] : 0;
        $this->inventory_point = (!empty($data['inventory_point'])) ? $data['inventory_point'] : 0;
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : null;
        $this->middle_name = (!empty($data['middle_name'])) ? $data['middle_name'] : null;
        $this->name_exam_type = (!empty($data['name_exam_type'])) ? $data['name_exam_type'] : null;
        $this->admin_last_name = (!empty($data['admin_last_name'])) ? $data['admin_last_name'] : null;
        $this->admin_first_name = (!empty($data['admin_first_name'])) ? $data['admin_first_name'] : null;
        $this->admin_middle_name = (!empty($data['admin_middle_name'])) ? $data['admin_middle_name'] : null;
        $this->total_points = (!empty($data['total_points'])) ? $data['total_points'] : 0;
        $this->final_score = (!empty($data['final_score'])) ? $data['final_score'] : 0;
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
                'name' => 'id_written_exam',
                'required' => FALSE,
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
                'name' => 'date',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'qa_point',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'rt_point',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'safety_point',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'specimen_point',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'testing_algo_point',
                'required' => true,
            ));
            $inputFilter->add(array(
                'name' => 'report_keeping_point',
                 'required'=>true,
            ));
            $inputFilter->add(array(
                'name' => 'EQA_PT_points',
                 'required'=>true,
            ));
            $inputFilter->add(array(
                'name' => 'ethics_point',
                 'required'=>true,
            ));

            $inputFilter->add(array(
                'name' => 'inventory_point',
                 'required'=>true,
            ));

            $inputFilter->add(array(
                'name' => 'practical',
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
