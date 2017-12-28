<?php

namespace Certification\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class PracticalExamTable extends AbstractTableGateway {

    private $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('practice_exam_id', 'exam_type', 'exam_admin', 'provider_id', 'Sample_testing_score', 'direct_observation_score', 'practical_total_score', 'date'));
        $sqlSelect->join('provider', ' provider.id = practical_exam.provider_id ', array('last_name', 'first_name', 'middle_name'), 'left')
                ->where(array('display' => 'yes'));
        $sqlSelect->order('practice_exam_id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function getPracticalExam($practice_exam_id) {
        $practice_exam_id = (int) $practice_exam_id;
        $rowset = $this->tableGateway->select(array('practice_exam_id' => $practice_exam_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $practice_exam_id");
        }
        return $row;
    }

    public function savePracticalExam(PracticalExam $practicalExam) {

        $date = $practicalExam->date;
        $date_explode = explode("-", $date);
        $newsdate = $date_explode[2] . '-' . $date_explode[1] . '-' . $date_explode[0];


        $data = array(
            'exam_type' => $practicalExam->exam_type,
            'exam_admin' => strtoupper($practicalExam->exam_admin),
            'provider_id' => $practicalExam->provider_id,
            'Sample_testing_score' => $practicalExam->Sample_testing_score,
            'direct_observation_score' => $practicalExam->direct_observation_score,
            'practical_total_score' => ($practicalExam->direct_observation_score + $practicalExam->Sample_testing_score) / 2,
            'date' => $newsdate
        );
//        print_r($data);
        $practice_exam_id = (int) $practicalExam->practice_exam_id;
        if ($practice_exam_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPracticalExam($practice_exam_id)) {
                $this->tableGateway->update($data, array('practice_exam_id' => $practice_exam_id));
            } else {
                throw new \Exception('Practical Exam id does not exist');
            }
        }
    }

    /**
     * get the last practical exam last id insert
     * @return $last_id integer
     */
    public function last_id() {
        $last_id = $this->tableGateway->lastInsertValue;
        return $last_id;
    }

    /**
     * insert practical_exam id to examination table
     * @param type $last_id 
     */
    public function insertToExamination($last_id) {
        $db = $this->tableGateway->getAdapter();

        $sql1 = 'select provider_id from practical_exam where practice_exam_id=' . $last_id;
        $statement1 = $db->query($sql1);
        $result1 = $statement1->execute();
        foreach ($result1 as $res1) {
            $provider = $res1['provider_id'];
        }

        $sql2 = 'SELECT count(*) as nombre FROM examination WHERE provider=' . $provider . ' and id_written_exam is not null and practical_exam_id is null';
        $statement2 = $db->query($sql2);
//        die($sql2);
        $result2 = $statement2->execute();
        foreach ($result2 as $res2) {
            $nombre = $res2['nombre'];
        }

        if ($nombre == 0) {
            $sql3 = 'insert into examination (practical_exam_id,provider) values (' . $last_id . ',' . $provider . ')';
            $statement3 = $db->query($sql3);
            $result3 = $statement3->execute();
        } else {
            $sql = 'UPDATE examination SET practical_exam_id=' . $last_id . ' WHERE provider=' . $provider;
            $db->getDriver()->getConnection()->execute($sql);
        }
    }

    /**
     * insert written and practical exam id to examination
     * @param type $written
     * @param type $last_id
     */
    public function examination($written, $last_id) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select provider_id from practical_exam where practice_exam_id=' . $last_id;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $provider = $res['provider_id'];
        }

        $sql2 = 'insert into examination (provider,id_written_exam,practical_exam_id) values (' . $provider . ',' . $written . ',' . $last_id . ')';
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

        $sql = 'SELECT COUNT(*) as nombre from (select  certification.id ,examination, final_decision, certification_issuer, date_certificate_issued, 
                date_certificate_sent, certification_type, provider,last_name, first_name, middle_name, certification_id,
                certification_reg_no, professional_reg_no,email,date_end_validity,facility_in_charge_email from certification, examination, provider where examination.id = certification.examination and provider.id = examination.provider and final_decision in ("failed","pending") and date_certificate_issued >' . $max_date . ' and provider=' . $provider . ') as tab';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;
    }

    public function getProviderName($written) {
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

    public function counPractical($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT count(*) as nombre FROM examination WHERE practical_exam_id is not null and id_written_exam is null and  provider=' . $provider . ' and add_to_certification="no"';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;
    }

    /**
     * 
     * @param type $written
     * @return type
     */
    public function countWritten2($written) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT count(*) as nombre FROM examination WHERE id_written_exam is not null and practical_exam_id is  null and id_written_exam=' . $written . ' and add_to_certification="no"';
//        die($sql);
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
//        die($nombre);
        return $nombre;
    }

    /**
     * find number of date before another attempt
     * @param type $provider
     * @return type
     */
    public function numberOfDays($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT DATEDIFF(now(),MAX(date_certificate_issued)) as nb_days from (SELECT provider, final_decision, date_certificate_issued, written_exam.id_written_exam , practical_exam.practice_exam_id ,last_name, first_name, middle_name, provider.id from examination, certification, written_exam,practical_exam, provider WHERE examination.id= certification.examination and examination.id_written_exam=written_exam.id_written_exam and practical_exam.practice_exam_id=examination.practical_exam_id and practical_exam.provider_id=provider.id and final_decision in ("pending","failed") and provider.id=' . $provider . ') as tab';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nb_days = $res['nb_days'];
        }
        return $nb_days;
    }

    public function getExamType($practical) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT exam_type  from practical_exam WHERE practice_exam_id=' . $practical;
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $exam_type = $res['exam_type'];
        }
        return $exam_type;
    }

    public function getProviderName2($practical) {
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

    public function CountPractical($practical_exam_id) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT count(practical_exam_id) as nombre FROM examination WHERE  practical_exam_id=' . $practical_exam_id;
//        die($sql1);
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }
        return $nombre;
    }

    public function deletePractical($practical_exam_id) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT max(id) as examination FROM  examination where practical_exam_id=' . $practical_exam_id;
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
        if (empty($id_written_exam)) {
            $sql2 = 'delete from examination WHERE id=' . $examination;
            $db->getDriver()->getConnection()->execute($sql2);
        } else {
            $sql2 = 'UPDATE examination SET practical_exam_id=null WHERE id=' . $examination;
            $db->getDriver()->getConnection()->execute($sql2);
        }
        $sql3 = 'Delete from practical_exam where practice_exam_id=' . $practical_exam_id;
        $db->getDriver()->getConnection()->execute($sql3);
    }

    public function examToValidate($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'SELECT count(*) as nombre FROM examination WHERE id_written_exam is not null and practical_exam_id is not null and add_to_certification="no" and provider=' . $provider;
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
