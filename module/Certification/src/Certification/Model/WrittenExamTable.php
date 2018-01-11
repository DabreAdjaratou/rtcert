<?php

namespace Certification\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class WrittenExamTable extends AbstractTableGateway {

    private $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id_written_exam', 'exam_type', 'provider_id', 'exam_admin', 'date', 'qa_point', 'rt_point',
            'safety_point', 'specimen_point', 'testing_algo_point', 'report_keeping_point', 'EQA_PT_points', 'ethics_point', 'inventory_point', 'total_points', 'final_score'));
        $sqlSelect->join('provider', ' provider.id= written_exam.provider_id ', array('last_name', 'first_name', 'middle_name'), 'left')
                ->where(array('display' => 'yes'));
        $sqlSelect->order('id_written_exam desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function getWrittenExam($id_written_exam) {
        $id_written_exam = (int) $id_written_exam;
        $rowset = $this->tableGateway->select(array('id_written_exam' => $id_written_exam));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id_written_exam");
        }
        return $row;
    }

    public function saveWrittenExam(WrittenExam $written_exam) {

        $date = $written_exam->date;
        $date_explode = explode("-", $date);
        $newsdate = $date_explode[2] . '-' . $date_explode[1] . '-' . $date_explode[0];


        $data = array(
            'exam_type' => $written_exam->exam_type,
            'provider_id' => $written_exam->provider_id,
            'exam_admin' => strtoupper($written_exam->exam_admin),
            'date' => $newsdate,
            'qa_point' => $written_exam->qa_point,
            'rt_point' => $written_exam->rt_point,
            'safety_point' => $written_exam->safety_point,
            'specimen_point' => $written_exam->specimen_point,
            'testing_algo_point' => $written_exam->testing_algo_point,
            'report_keeping_point' => $written_exam->report_keeping_point,
            'EQA_PT_points' => $written_exam->EQA_PT_points,
            'ethics_point' => $written_exam->ethics_point,
            'inventory_point' => $written_exam->inventory_point,
            'total_points' => $written_exam->qa_point + $written_exam->rt_point + $written_exam->safety_point + $written_exam->specimen_point + $written_exam->testing_algo_point + $written_exam->report_keeping_point + $written_exam->EQA_PT_points + $written_exam->ethics_point + $written_exam->inventory_point,
            'final_score' => (($written_exam->qa_point + $written_exam->rt_point + $written_exam->safety_point + $written_exam->specimen_point + $written_exam->testing_algo_point + $written_exam->report_keeping_point + $written_exam->EQA_PT_points + $written_exam->ethics_point + $written_exam->inventory_point) * 100) / 25
        );

        $id_written_exam = (int) $written_exam->id_written_exam;
        if ($id_written_exam == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getWrittenExam($id_written_exam)) {
                $this->tableGateway->update($data, array('id_written_exam' => $id_written_exam));
            } else {
                throw new \Exception('Written Exam id does not exist');
            }
        }
    }

    public function last_id() {
        $last_id = $this->tableGateway->lastInsertValue;
//        die($last_id);
        return $last_id;
    }

    /**
     * insert written exam id to  examination
     * @param type $last_id
     */
    public function insertToExamination($last_id) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select provider_id from written_exam where id_written_exam=' . $last_id;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $provider = $res['provider_id'];
        }

        $sql2 = 'SELECT count(*) as nombre FROM examination WHERE provider=' . $provider . ' and id_written_exam is null and practical_exam_id is not null';
        $statement2 = $db->query($sql2);
        $result2 = $statement2->execute();
        foreach ($result2 as $res2) {
            $nombre = $res2['nombre'];
        }

        if ($nombre == 0) {
            $sql2 = 'insert into examination (id_written_exam,provider) values (' . $last_id . ',' . $provider . ')';
            $statement2 = $db->query($sql2);
            $result2 = $statement2->execute();
        } else {
            $sql = 'UPDATE examination SET id_written_exam=' . $last_id . ' WHERE provider=' . $provider;
            $db->getDriver()->getConnection()->execute($sql);
        }
    }

    /**
     * insert written and practical exam id to examination
     * @param type $written
     * @param type $last_id
     */
    public function examination($last_id, $practical) {

        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select provider_id from written_exam where id_written_exam=' . $last_id;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $provider = $res['provider_id'];
        }

        $sql2 = 'insert into examination (provider,id_written_exam,practical_exam_id) values (' . $provider . ',' . $last_id . ',' . $practical . ')';
        $statement2 = $db->query($sql2);
        $result2 = $statement2->execute();
    }

    /**
     * count the number of  attempt 
     * @return type $nombre integer
     */
    public function attemptNumber($provider) {
        $db = $this->tableGateway->getAdapter();

        $sql1 = 'select max(date_certificate_issued) as max_date  from certification, examination WHERE certification.examination=examination.id and final_decision="certified" and provider=' . $provider;
        $statement1 = $db->query($sql1);
        $result1 = $statement1->execute();
        foreach ($result1 as $res1) {
            $max_date = $res1['max_date'];
        }

        if ($max_date == null) {
            $max_date = '0000-00-00';
        }
//        die($max_date);

        $sql = 'SELECT COUNT(*) as nombre from (select  certification.id ,examination, final_decision, certification_issuer, date_certificate_issued, 
                date_certificate_sent, certification_type, provider,last_name, first_name, middle_name, certification_id,
                certification_reg_no, professional_reg_no,email,date_end_validity,facility_in_charge_email from certification, examination, provider where examination.id = certification.examination and provider.id = examination.provider and final_decision in ("failed","pending") and date_certificate_issued >' . $max_date . ' and provider=' . $provider . ') as tab';
//        die($sql);
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
//        die($nombre);
        return $nombre;
    }

    public function counWritten($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT count(*) as nombre FROM examination WHERE id_written_exam is not null and practical_exam_id is null and  provider=' . $provider . ' and add_to_certification="no"';
//        die($sql3);
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
//        die($nombre);
        return $nombre;
    }

    public function getProviderName($practical) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select id, last_name, first_name, middle_name from provider , practical_exam where provider.id=practical_exam.provider_id and practice_exam_id=' . $practical;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        $selectData = array();

        foreach ($result as $res) {
            $selectData['name'] = $res['last_name'] . ' ' . $res['first_name'] . ' ' . $res['middle_name'];
            $selectData['id'] = $res['id'];
        }
        return $selectData;
    }

    public function countPractical2($practical) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT count(*) as nombre FROM examination WHERE practical_exam_id is not null and id_written_exam is null and practical_exam_id=' . $practical . ' and add_to_certification="no"';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;
    }

    /**
     * find number of date before another attempt
     * @param type $provider
     * @return type
     */
    public function numberOfDays($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT DATEDIFF(now(),MAX(date_certificate_issued)) as nb_days from (SELECT provider, final_decision, date_certificate_issued, written_exam.id_written_exam , practical_exam.practice_exam_id ,last_name, first_name, middle_name, provider.id from examination, certification, written_exam,practical_exam, provider WHERE examination.id= certification.examination and examination.id_written_exam=written_exam.id_written_exam and practical_exam.practice_exam_id=examination.practical_exam_id and written_exam.provider_id=provider.id and final_decision in ("pending","failed") and provider.id=' . $provider . ') as tab';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nb_days = $res['nb_days'];
        }
        return $nb_days;
    }

    public function getExamType($written) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT exam_type from written_exam WHERE id_written_exam=' . $written;
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $exam_type = $res['exam_type'];
        }
        return $exam_type;
    }

    public function getProviderName2($written) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select id, last_name, first_name, middle_name from provider , written_exam where provider.id=written_exam.provider_id and id_written_exam=' . $written;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        $selectData = array();

        foreach ($result as $res) {
            $selectData['name'] = $res['last_name'] . ' ' . $res['first_name'] . ' ' . $res['middle_name'];
            $selectData['id'] = $res['id'];
        }
        return $selectData;
    }

     public function CountWritten($id_written_exam) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT count(id_written_exam) as nombre FROM examination WHERE  id_written_exam=' . $id_written_exam;
//        die($sql1);
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;
    }
    
    public function deleteWritten($id_written_exam) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT max(id) as examination FROM  examination where id_written_exam=' . $id_written_exam;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $examination = $res['examination'];
        }

         $sql4 = 'select id_written_exam,practical_exam_id from examination where id=' . $examination;
        $statement4 = $db->query($sql4);
        $result4 = $statement4->execute();
        foreach ($result4 as $res4) {
            $id_written_exam = $res4['id_written_exam'];
            $practical_exam_id = $res4['practical_exam_id'];
        }
        if (empty($practical_exam_id)) {
            $sql2 = 'delete from examination WHERE id=' . $examination;
            $db->getDriver()->getConnection()->execute($sql2);
        } else {
            $sql2 = 'UPDATE examination SET id_written_exam=null WHERE id=' . $examination;
            $db->getDriver()->getConnection()->execute($sql2);
        }
        $sql3 = 'Delete from written_exam where id_written_exam='.$id_written_exam;
        $db->getDriver()->getConnection()->execute($sql3);
        
        
        
      
    }
    
public function examToValidate($provider){
         $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT count(*) as nombre FROM examination WHERE id_written_exam is not null and practical_exam_id is not null and add_to_certification="no" and provider='.$provider;
//        die($sql);
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
//        die($nombre);
        return $nombre;
    }

}
