<?php
 namespace Certification\Form;

 use Zend\Form\Form;
 use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

 class ExaminationForm extends Form
 {
     public function __construct(AdapterInterface $dbAdapter)
     {
         $this->adapter = $dbAdapter;
         // we want to ignore the name passed
         parent::__construct('examination');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'provider',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Provider',
                 'empty_option' => 'Please choose an Type',
                'value_options' => $this->getListProvider()
             ),
         ));
         $this->add(array(
             'name' => 'id_written_exam',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Written Exam',
                 'empty_option' => 'Please choose an Type',
                'value_options' => $this->getListwritten()
             ),
         ));
         $this->add(array(
             'name' => 'practical_exam_id',
             'type' => 'Zend\Form\Element\Select',
             'options' => array(
                 'label' => 'Practical Exam',
                 'empty_option' => 'Please choose an Type',
                'value_options' => $this->getListpractical()
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
     
      public function getListProvider() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT id,certification_id,last_name,first_name,middle_name FROM provider order by last_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['id']] = $res['last_name'].' '. $res['first_name'].' '. $res['middle_name'].' '. $res['certification_id'];
           
        }
        return $selectData;
    }
    
    public function getListwritten() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT id_written_exam,final_score,exam_type,last_name,first_name,middle_name FROM written_exam,provider where provider.certification_id=written_exam.provider_id order by last_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['id_written_exam']] = $res['exam_type'].' '. $res['final_score'].' '. $res['last_name'].' '. $res['first_name'].' '. $res['middle_name'];
           
        }
        return $selectData;
    }
    
    public function getListpractical() {
        $dbAdapter = $this->adapter;
        $sql = 'SELECT practice_exam_id,practical_total_score,exam_type,last_name,first_name,middle_name FROM practical_exam,provider where provider.certification_id=practical_exam.provider_id order by last_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();

        $selectData = array();

        foreach ($result as $res) {
            $selectData[$res['practice_exam_id']] = $res['exam_type'].' '. $res['practical_total_score'].' '. $res['last_name'].' '. $res['first_name'].' '. $res['middle_name'];
           
        }
        return $selectData;
    }
 }
