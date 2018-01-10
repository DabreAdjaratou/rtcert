<?php

namespace Certification\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class RecertificationTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('recertification_id', 'due_date', 'reminder_type', 'reminder_sent_to', 'name_of_recipient', 'date_reminder_sent', 'provider_id'))
                ->join('provider', 'provider.id=recertification.provider_id', array('certification_id', 'last_name', 'first_name', 'middle_name'), 'left');

        $sqlSelect->order('recertification_id desc');
        $resultSet = $this->tableGateway->selectWith($sqlSelect);
//        die(print_r($resultSet));
        return $resultSet;
    }

    public function getRecertification($recertification_id) {
        $recertification_id = (int) $recertification_id;
        $rowset = $this->tableGateway->select(array('recertification_id' => $recertification_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $recertification_id");
        }
        return $row;
    }

    public function saveRecertification(Recertification $recertification) {

        $due_date = $recertification->due_date;
        $date_explode = explode("-", $due_date);
        $newsdate = $date_explode[2] . '-' . $date_explode[1] . '-' . $date_explode[0];

        $date_reminder_sent = $recertification->date_reminder_sent;
        $date_explode2 = explode("-", $date_reminder_sent);
        $newsdate2 = $date_explode2[2] . '-' . $date_explode2[1] . '-' . $date_explode2[0];

        $data = array(
            'due_date' => $newsdate,
            'provider_id' => $recertification->provider_id,
            'reminder_type' => $recertification->reminder_type,
            'reminder_sent_to' => $recertification->reminder_sent_to,
            'name_of_recipient' => strtoupper($recertification->name_of_recipient),
            'date_reminder_sent' => $newsdate2,
        );
//die((print_r($data)));
        $recertification_id = (int) $recertification->recertification_id;
        if ($recertification_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRecertification($recertification_id)) {
                $this->tableGateway->update($data, array('recertification_id' => $recertification_id));
            } else {
                throw new \Exception('Recertification id does not exist');
            }
        }
    }

    /**
     * select reminder witch must be  send
     * @return type
     */
    public function fetchAll2() {
        $db = $this->tableGateway->getAdapter();
        $sqlSelect = "select  certification.id ,examination, final_decision, certification_issuer, date_certificate_issued, "
                . "date_certificate_sent, certification_type, provider,last_name, first_name, middle_name, certification_id,"
                . " certification_reg_no, professional_reg_no,email,date_end_validity,facility_in_charge_email from certification, examination, provider where "
                . "examination.id = certification.examination and provider.id = examination.provider and final_decision='certified' and certificate_sent = 'yes' and reminder_sent='no' and"
                . " datediff(now(),date_end_validity) >=-60 order by certification.id asc";
        $statement = $db->query($sqlSelect);
        $resultSet = $statement->execute();

        return $resultSet;
    }

    public function ReminderSent($certification_id) {
        $db = $this->tableGateway->getAdapter();
        $sql = "UPDATE certification set reminder_sent='yes' where id=" . $certification_id;
        $db->getDriver()->getConnection()->execute($sql);
    }

    public function certificationInfo($certification_id) {
        $db = $this->tableGateway->getAdapter();
        $sql2 = 'SELECT provider.id as provider_id, last_name, first_name, middle_name, certification.date_end_validity as due_date from provider, examination , certification WHERE provider.id=examination.provider AND examination.id=certification.examination and certification.id=' . $certification_id;
        $statement2 = $db->query($sql2);
        $result2 = $statement2->execute();

        $selectData = array();

        foreach ($result2 as $res2) {
            $selectData['name'] = $res2['last_name'] . ' ' . $res2['first_name'] . ' ' . $res2['middle_name'];
            $selectData['id'] = $res2['provider_id'];
            $selectData['due_date'] = $res2['due_date'];
        }

        return $selectData;
    }

    public function report($startDate, $endDate, $decision, $typeHiv, $jobTitle, $region, $district, $facility, $reminder_type, $reminder_sent_to, $startDate2, $endDate2) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'select certification.certification_issuer,certification.certification_type, certification.date_certificate_issued,certification.date_end_validity, certification.final_decision,provider.certification_reg_no, provider.certification_id, provider.professional_reg_no, provider.first_name, provider.last_name, provider.middle_name,certification_regions.region_name,certification_districts.district_name, provider.type_vih_test, provider.phone,provider.email, provider.prefered_contact_method,provider.current_jod, provider.time_worked,provider.test_site_in_charge_name, provider.test_site_in_charge_phone,provider.test_site_in_charge_email, provider.facility_in_charge_name, provider.facility_in_charge_phone, provider.facility_in_charge_email,certification_facilities.facility_name, written_exam.exam_type as written_exam_type,written_exam.exam_admin as written_exam_admin,written_exam.date as written_exam_date,written_exam.qa_point,written_exam.rt_point,written_exam.safety_point,written_exam.specimen_point, written_exam.testing_algo_point, written_exam.report_keeping_point,written_exam.EQA_PT_points, written_exam.ethics_point, written_exam.inventory_point, written_exam.total_points,written_exam.final_score, practical_exam.exam_type as practical_exam_type , practical_exam.exam_admin as practical_exam_admin , practical_exam.Sample_testing_score, practical_exam.direct_observation_score,practical_exam.practical_total_score,practical_exam.date as practical_exam_date, recertification.reminder_type, recertification.reminder_sent_to, recertification.name_of_recipient, recertification.date_reminder_sent from certification,examination,written_exam,practical_exam,provider,certification_districts, certification_facilities, certification_regions,recertification WHERE certification.examination= examination.id and examination.id_written_exam= written_exam.id_written_exam and examination.practical_exam_id= practical_exam.practice_exam_id and provider.id=examination.provider and provider.facility_id=certification_facilities.id and provider.region= certification_regions.id  and provider.district=certification_districts.id and provider.id=recertification.provider_id and recertification.due_date=certification.date_end_validity';
        if (!empty($startDate) && !empty($endDate)) {
            $sql = $sql . ' and  recertification.due_date >="' . $startDate . '" and recertification.due_date <="' . $endDate . '"';
        }

        if (!empty($decision)) {
            $sql = $sql . ' and certification.final_decision="' . $decision . '"';
        }

        if (!empty($typeHiv)) {
            $sql = $sql . ' and provider.type_vih_test="' . $typeHiv . '"';
        }
        if (!empty($jobTitle)) {
            $sql = $sql . ' and provider.current_jod="' . $jobTitle . '"';
        }

        if (!empty($region)) {
            $sql = $sql . ' and certification_regions.id=' . $region;
        }

        if (!empty($district)) {
            $sql = $sql . ' and certification_districts.id=' . $district;
        }

        if (!empty($facility)) {
            $sql = $sql . ' and certification_facilities.id=' . $facility;
        }

        if (!empty($reminder_type)) {
            $sql = $sql . ' and recertification.reminder_type="' . $reminder_type . '"';
        }

        if (!empty($reminder_sent_to)) {
            $sql = $sql . ' and recertification.reminder_sent_to="' . $reminder_sent_to . '"';
        }

        if (!empty($startDate2) && !empty($endDate2)) {
            $sql = $sql . ' and recertification.date_reminder_sent >="' . $startDate2 . '" and recertification.date_reminder_sent <="' . $endDate2 . '"';
        }
//        die($sql);

        $statement = $db->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getRegions() {
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT id, region_name FROM certification_regions  ORDER by region_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['id']] = $res['region_name'];
        }
//        die(print_r($selectData));
        return $selectData;
    }

}
