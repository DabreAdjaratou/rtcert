<?php

namespace Certification\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Debug\Debug;
use Zend\Db\Sql\Expression;
//use Zend\Db\Sql\Where;
use \Application\Model\GlobalTable;
use \Application\Service\CommonService;

class CertificationTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway, Adapter $adapter) {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
    }


    public function getQuickStats(){
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('c'=>'certification'))
                     ->columns(
                            array(  "total" => new Expression('COUNT(*)'),
                                    "sent" => new Expression("SUM(CASE 
                                                                    WHEN ((c.certificate_sent = 'yes')) THEN 1
                                                                    ELSE 0
                                                                    END)"),
                                    "toBeSent" => new Expression("SUM(CASE 
                                                                        WHEN ((c.certificate_sent != 'yes' AND c.final_decision='Certified')) THEN 1
                                                                        ELSE 0
                                                                        END)"),
                                    "certified" => new Expression("SUM(CASE 
                                                                        WHEN ((c.certification_type = 'initial'  AND c.date_certificate_issued >= DATE_FORMAT(NOW(),'%Y-%m-01') 
                                                                        AND c.date_certificate_issued <  DATE_FORMAT(NOW(),'%Y-%m-01') + INTERVAL 1 MONTH)) THEN 1
                                                                        ELSE 0
                                                                        END)"),                                                                        
                                    "recertified" => new Expression("SUM(CASE 
                                                                        WHEN ((c.certification_type !=  'initial' AND c.date_certificate_issued >= DATE_FORMAT(NOW(),'%Y-%m-01') 
                                                                        AND c.date_certificate_issued <  DATE_FORMAT(NOW(),'%Y-%m-01') + INTERVAL 1 MONTH)) THEN 1
                                                                        ELSE 0
                                                                        END)"),                       
                                    "pending" => new Expression("SUM(CASE 
                                                                        WHEN ((c.final_decision = 'Pending')) THEN 1
                                                                        ELSE 0
                                                                        END)"),                                                                        
                            )
                );  
                $queryStr = $sql->getSqlStringForSqlObject($query);
                $res = $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
                return $res[0];
    }

    public function getCertificationPieChartResults($params){
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('c'=>'certification'))
                     ->columns(array("total_certification" => new Expression('COUNT(*)')))
                     ->join(array('e'=>'examination'),'e.id=c.examination',array())
                     ->join(array('p'=>'provider'),'p.id=e.provider',array())
                     ->join(array('l_d'=>'location_details'),'l_d.location_id=p.region',array('location_name'))
                     ->where('(c.final_decision = "Certified" OR c.final_decision = "certified") AND date_certificate_issued > DATE_SUB(NOW(), INTERVAL 12 MONTH) AND date_end_validity > NOW()')
                     ->group('p.region');
        $queryStr = $sql->getSqlStringForSqlObject($query);
        return $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
    
    public function getCertificationBarChartResults($params){
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = new Sql($dbAdapter);
        $start = strtotime(date("Y", strtotime("-1 year")).'-'.date('m', strtotime('+1 month', strtotime('-1 year'))));
        $end = strtotime(date('Y').'-'.date('m'));
        $j = 0;
        $certificationResult = array();
        while($start <= $end){
            $month = date('m', $start); $year = date('Y', $start); $monthYearFormat = date("M 'y", $start);
            $query = $sql->select()->from(array('c'=>'certification'))
                         ->columns(
                                   array(
                                        "certifications" => new Expression("SUM(CASE 
                                                                                    WHEN ((c.certification_type IS NOT NULL AND c.certification_type != 'NULL' AND c.certification_type != '' AND (c.certification_type = 'Initial' OR c.certification_type = 'initial'))) THEN 1
                                                                                    ELSE 0
                                                                                    END)"),
                                         "recertifications" => new Expression("SUM(CASE 
                                                                                    WHEN ((c.certification_type IS NOT NULL AND c.certification_type != 'NULL' AND c.certification_type != '' AND (c.certification_type = 'Recertification' OR c.certification_type = 'recertification'))) THEN 1
                                                                                    ELSE 0
                                                                                    END)")
                                    )
                                )
                         ->where('(c.final_decision = "Certified" OR c.final_decision = "certified") AND Month(date_certificate_issued)="'.$month.'" AND Year(date_certificate_issued)="'.$year.'" AND date_end_validity > NOW()');
            $queryStr = $sql->getSqlStringForSqlObject($query);
            $result = $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            $certificationResult['certification']['Certifications'][$j] = (isset($result[0]["certifications"]))?$result[0]["certifications"]:0;
            $certificationResult['certification']['Recertifications'][$j] = (isset($result[0]["recertifications"]))?$result[0]["recertifications"]:0;
            $certificationResult['month'][$j] = $monthYearFormat;
          $start = strtotime("+1 month", $start);
          $j++;
        }
      return $certificationResult;
    }
    
    /**
     * select all certified tester
     * @return type
     */
    public function fetchAll() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'examination', 'final_decision', 'certification_issuer', 'date_certificate_issued', 'date_certificate_sent', 'certification_type'));
        $sqlSelect->join('examination', 'examination.id = certification.examination ', array('provider'), 'left')
                ->join('provider', 'provider.id = examination.provider ', array('last_name', 'first_name', 'middle_name', 'certification_id', 'certification_reg_no', 'professional_reg_no', 'email'), 'left')
                ->where(array('final_decision' => 'certified'));
        $sqlSelect->order('id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    /**
     * select tester who are pending or failed to certification
     * @return type
     */
    public function fetchAll2() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'examination', 'final_decision', 'certification_issuer', 'date_certificate_issued', 'date_certificate_sent', 'certification_type'));
        $sqlSelect->join('examination', 'examination.id = certification.examination ', array('provider'), 'left')
                ->join('provider', 'provider.id = examination.provider ', array('last_name', 'first_name', 'middle_name', 'certification_id', 'certification_reg_no', 'professional_reg_no'), 'left')
                ->where(array('final_decision' => 'failed'))
                ->where(array('final_decision' => 'pending'), \Zend\Db\Sql\Where::OP_OR);
        $sqlSelect->order('id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function getCertification($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getProvider($id){
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT provider FROM certification, examination WHERE certification.examination=examination.id AND certification.id='.$id;
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $provider_id = $res['provider'];
        }
      return $provider_id;
    }
    
    public function saveCertification(Certification $certification) {
        $dbAdapter = $this->adapter;
        $globalDb = new GlobalTable($dbAdapter);
        $monthValid = $globalDb->getGlobalValue('month-valid');
        $validity_end = (isset($monthValid) && trim($monthValid)!= '')?' + '.$monthValid.' month':' + 2 year';
        $date_issued = $certification->date_certificate_issued;
        $date_explode = explode("-", $date_issued);
        $newsdate = $date_explode[2] . '-' . $date_explode[1] . '-' . $date_explode[0];
        if (isset($certification->date_certificate_sent) && $certification->date_certificate_sent!= '' && $certification->date_certificate_sent!= '0000-00-00') {
            $certification->date_certificate_sent = date("Y-m-d", strtotime($certification->date_certificate_sent));
        }
        if($certification->certification_type == 'Recertification' || $certification->certification_type == 'recertification'){
            $db = $this->tableGateway->getAdapter();
            $sql = 'select date_end_validity from certification, examination, provider WHERE certification.examination=examination.id and examination.provider=provider.id and final_decision="certified" and provider=' . $certification->provider.' ORDER BY date_certificate_issued DESC LIMIT 1';
            $statement = $db->query($sql);
            $result = $statement->execute();
            foreach ($result as $res) {
                $certification_validity = $res['date_end_validity'];
            }
        }
        if(isset($certification_validity) && $certification_validity!= null && $certification_validity!= '' && $certification_validity!= '0000-00-00'){
            $date_end = date("Y-m-d", strtotime($certification_validity . $validity_end));
        }else{
            $date_end = date("Y-m-d", strtotime($date_issued . $validity_end));
        }

        $data = array(
            'examination' => $certification->examination,
            'final_decision' => $certification->final_decision,
            'certification_issuer' => strtoupper($certification->certification_issuer),
            'date_certificate_issued' => $newsdate,
            'date_certificate_sent' => $certification->date_certificate_sent,
            'certification_type' => $certification->certification_type
        );
//        die(print_r($data));
        $id = (int) $certification->id;
        if ($id == 0) {
            $data['date_end_validity'] = $date_end;
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCertification($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('certification id does not exist');
            }
        }
    }

    public function last_id() {
        $last_id = $this->tableGateway->lastInsertValue;
//        die($last_id);
        return $last_id;
    }

    public function updateExamination($last_id) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select examination from certification where id=' . $last_id;
        $statement = $db->query($sql1);
        $result = $statement->execute();
        foreach ($result as $res) {
            $examination = $res['examination'];
        }

//        die ($examination);

        $sql = 'UPDATE examination SET add_to_certification= "yes" WHERE id=' . $examination;

        $db->getDriver()->getConnection()->execute($sql);
    }

    public function setToActive($last_id) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'SELECT id_written_exam,practical_exam_id,final_decision FROM certification ,examination WHERE certification.examination=examination.id and certification.id=' . $last_id;
        $statement1 = $db->query($sql1);
        $result1 = $statement1->execute();

        foreach ($result1 as $res1) {
            $written = $res1['id_written_exam'];
            $practical = $res1['practical_exam_id'];
            $decision = $res1['final_decision'];
        }
        if ((strcasecmp($decision, 'Certified') == 0) || (strcasecmp($decision, 'failed') == 0)) {
// 
            $sql2 = "UPDATE written_exam SET display='no' WHERE id_written_exam=" . $written;
            $statement2 = $db->query($sql2);
            $result2 = $statement2->execute();

            $sql3 = "UPDATE practical_exam SET display='no' WHERE practice_exam_id=" . $practical;
            $statement3 = $db->query($sql3);
            $result3 = $statement3->execute();
        }
    }

    public function certificationType($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql1 = 'select certification_id, date_end_validity from certification, examination, provider WHERE certification.examination=examination.id and examination.provider=provider.id and final_decision="certified" and provider=' . $provider.' ORDER BY date_certificate_issued DESC LIMIT 1';
        //die($sql1);
        $statement = $db->query($sql1);
        $result = $statement->execute();
        $certification_id = null;
        foreach ($result as $res) {
            $certification_id = $res['certification_id'];
            $date_end_validity = $res['date_end_validity'];
        }
        if(isset($certification_id) && $certification_id!= null && $certification_id!= ''){
            $dbAdapter = $this->adapter;
            $globalDb = new GlobalTable($dbAdapter);
            $monthFlexLimit = $globalDb->getGlobalValue('month-flex-limit');
            $startdate = strtotime(date('Y-m-d'));
            $enddate = strtotime($date_end_validity);
            $startyear = date('Y', $startdate);
            $endyear = date('Y', $enddate);
            $startmonth = date('m', $startdate);
            $endmonth = date('m', $enddate);
            $remmonths = abs((($endyear - $startyear) * 12) + ($endmonth - $startmonth));
            if($remmonths > 0 && $remmonths > $monthFlexLimit){
                $certification_id = null;
            }
        }
        return $certification_id;
    }

    public function certificationId($provider) {
        $db = $this->tableGateway->getAdapter();
        $dbAdapter = $this->adapter;
        $globalDb = new GlobalTable($dbAdapter);
        $sql = 'SELECT MAX(certification_key) as max FROM provider';
        $statement = $db->query($sql);
        $result = $statement->execute();
        $certificationKey = 1;
        foreach ($result as $res) {
            $certificationKey = ($res['max']+1);
        }
        
        $certificatePrefix = ($globalDb->getGlobalValue('certificate-prefix')!= null && $globalDb->getGlobalValue('certificate-prefix')!= '')?$globalDb->getGlobalValue('certificate-prefix'):'';
        $certification_id = $certificatePrefix . sprintf("%04d", $certificationKey);
        $sql2 = "UPDATE provider SET certification_id='" . $certification_id . "', certification_key = '.$certificationKey.' WHERE id=" . $provider;

        $db->getDriver()->getConnection()->execute($sql2);
    }

    /**
     * select certified testers who certificate are not yet sent
     * @return type
     */
    public function fetchAll3() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('id', 'examination', 'final_decision', 'certification_issuer', 'date_certificate_issued', 'date_certificate_sent', 'certification_type'));
        $sqlSelect->join('examination', 'examination.id = certification.examination ', array('provider'), 'left')
                ->join('provider', 'provider.id = examination.provider ', array('last_name', 'first_name', 'middle_name', 'certification_id', 'certification_reg_no', 'professional_reg_no', 'email', 'facility_in_charge_email'), 'left')
                ->where(array('final_decision' => 'certified'))
                ->where(array('certificate_sent' => 'no'));
        $sqlSelect->order('id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function countCertificate() {
        $db = $this->tableGateway->getAdapter();
        $sqlSelect = 'select COUNT(*)  as nb from  (select certification.id, examination, final_decision, certification_issuer, date_certificate_issued, date_certificate_sent, certification_type, date_end_validity,examination.provider, last_name, first_name, middle_name, certification_id, certification_reg_no, professional_reg_no, email, facility_in_charge_email from certification,examination,provider             where examination.id = certification.examination and provider.id = examination.provider and final_decision ="certified" and certificate_sent ="no") as tab';
        $statement = $db->query($sqlSelect);

        $result = $statement->execute();
        foreach ($result as $res) {
            $nb = $res['nb'];
        }
        return $nb;
    }

    public function countReminder() {
        $db = $this->tableGateway->getAdapter();
        $sqlSelect = 'select COUNT(*) as nb2 from (select  certification.id ,examination, final_decision, certification_issuer, date_certificate_issued, 
                date_certificate_sent, certification_type, provider,last_name, first_name, middle_name, certification_id,
                certification_reg_no, professional_reg_no,email,date_end_validity,facility_in_charge_email from certification, examination, provider where examination.id = certification.examination and provider.id = examination.provider and final_decision="certified" and certificate_sent = "yes" and reminder_sent="no" and datediff(now(),date_end_validity) >=-60 order by certification.id asc) as tab';
        $statement = $db->query($sqlSelect);

        $result = $statement->execute();
        foreach ($result as $res) {
            $nb2 = $res['nb2'];
        }
        return $nb2;
    }

    public function CertificateSent($provider) {
        $db = $this->tableGateway->getAdapter();
        $sql = "UPDATE certification set certificate_sent='yes' where id=" . $provider;
        $db->getDriver()->getConnection()->execute($sql);
    }

    public function report($startDate, $endDate, $decision, $typeHiv, $jobTitle, $country, $region, $district, $facility) {
        $db = $this->tableGateway->getAdapter();
        $sql = 'select certification.certification_issuer,certification.certification_type, certification.date_certificate_issued,certification.date_end_validity, certification.final_decision,provider.certification_reg_no, provider.certification_id, provider.professional_reg_no, provider.first_name, provider.last_name, provider.middle_name,l_d_r.location_name as region_name,l_d_d.location_name as district_name,c.country_name, provider.type_vih_test, provider.phone,provider.email, provider.prefered_contact_method,provider.current_jod, provider.time_worked,provider.test_site_in_charge_name, provider.test_site_in_charge_phone,provider.test_site_in_charge_email, provider.facility_in_charge_name, provider.facility_in_charge_phone, provider.facility_in_charge_email,certification_facilities.facility_name, written_exam.exam_type as written_exam_type,written_exam.exam_admin as written_exam_admin,written_exam.date as written_exam_date,written_exam.qa_point,written_exam.rt_point,written_exam.safety_point,written_exam.specimen_point, written_exam.testing_algo_point, written_exam.report_keeping_point,written_exam.EQA_PT_points, written_exam.ethics_point, written_exam.inventory_point, written_exam.total_points,written_exam.final_score, practical_exam.exam_type as practical_exam_type , practical_exam.exam_admin as practical_exam_admin , practical_exam.Sample_testing_score, practical_exam.direct_observation_score,practical_exam.practical_total_score,practical_exam.date as practical_exam_date from certification,examination,written_exam,practical_exam,provider,location_details as l_d_r,location_details as l_d_d, country as c, certification_facilities WHERE certification.examination= examination.id and examination.id_written_exam= written_exam.id_written_exam and examination.practical_exam_id= practical_exam.practice_exam_id and provider.id=examination.provider and provider.facility_id=certification_facilities.id and provider.region= l_d_r.location_id and provider.district=l_d_d.location_id and l_d_r.country=c.country_id';

        if (!empty($startDate) && !empty($endDate)) {
            $sql = $sql . ' and  certification.date_certificate_issued between"' . $startDate . '" and "' . $endDate . '"';
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

        if (!empty($country)) {
            $sql = $sql . ' and c.country_id=' . $country;
        }
        
        if (!empty($region)) {
            $sql = $sql . ' and l_d_r.location_id=' . $region;
        }

        if (!empty($district)) {
            $sql = $sql . ' and l_d_d.location_id=' . $district;
        }

        if (!empty($facility)) {
            $sql = $sql . ' and certification_facilities.id=' . $facility;
        }
//        die($sql);
        //echo $sql;die;
        $statement = $db->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function getAllActiveCountries(){
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT * FROM country ORDER by country_name asc ';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        $selectData = [];
        foreach ($result as $res) {
            $selectData[$res['country_id']] = ucwords($res['country_name']);
        }
//        die(print_r($selectData));
        return $selectData;
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

    public function SelectTexteHeader() {
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT id, header_texte FROM pdf_header_texte';
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $header_text = $res['header_texte'];
        }
//        die($header_texte);
        return $header_text;
    }

    public function insertTextHeader($text) {
        $db = $this->tableGateway->getAdapter();

        $sql = 'SELECT count(*) as nombre FROM pdf_header_texte';
        $statement = $db->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $nombre = $res['nombre'];
        }

        if ($nombre == 0) {

            $sql2 = 'insert into pdf_header_texte (header_texte) values ("' . $text . '")';
            $statement2 = $db->query($sql2);
            $result2 = $statement2->execute();
        } else {
            $sql3 = 'TRUNCATE pdf_header_texte';
            $statement3 = $db->query($sql3);
            $result3 = $statement3->execute();

            $sql2 = 'insert into pdf_header_texte (header_texte) values ("' . $text . '")';
            $statement2 = $db->query($sql2);
            $result2 = $statement2->execute();
        }
    }
    
    public function getCertificationValiditydate($id){
        $dbAdapter = $this->tableGateway->getAdapter();
        $sql = 'SELECT date_end_validity FROM certification WHERE certification.id = '.$id;
        $statement = $dbAdapter->query($sql);
        $result = $statement->execute();
        foreach ($result as $res) {
            $date_end_validity = $res['date_end_validity'];
        }
      return $date_end_validity;
    }

}
