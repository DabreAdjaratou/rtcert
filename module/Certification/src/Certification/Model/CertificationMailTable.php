<?php

namespace Certification\Model;

use Zend\Db\TableGateway\TableGateway;

class CertificationMailTable {

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function getCertificationMail($mail_id) {
        $mail_id = (int) $mail_id;
        $rowset = $this->tableGateway->select(array('mail_id' => $mail_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $mail_id");
        }
        return $row;
    }

    public function saveCertificationMail(CertificationMail $CertificationMail) {
        $data = array(
            'to_email' => $CertificationMail->to_email,
            'cc' => $CertificationMail->cc,
            'bcc' => $CertificationMail->bcc,
            'type' => $CertificationMail->type,
            'mail_date' => date('Y-m-d'),
        );

        $mail_id = (int) $CertificationMail->mail_id;
        if ($mail_id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCertificationMail($mail_id)) {
                $this->tableGateway->update($data, array('mail_id' => $mail_id));
            } else {
                throw new \Exception('Mail id does not exist');
            }
        }
    }

    /**
     * update certification table when mail is sent with certificate
     * @param type $certification_id
     */
    public function dateCertificateSent($certification_id) {
        $db = $this->tableGateway->getAdapter();

        $sql = "UPDATE certification SET date_certificate_sent='" . date('Y-m-d') . "', certificate_sent='yes' WHERE id=" . $certification_id;
//        die($sql);
        $db->getDriver()->getConnection()->execute($sql);
    }
    
    public function reminderSent($certification_id) {
        $db = $this->tableGateway->getAdapter();

        $sql = "UPDATE certification SET  reminder_sent='yes' WHERE id=" . $certification_id;
//        die($sql);
        $db->getDriver()->getConnection()->execute($sql);
    }

    public function fetchAll() {
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('mail_id', 'to_email', 'cc', 'bcc', 'type', 'mail_date'));
        $sqlSelect->order('mail_date asc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        return $resultSet;
    }

    public function insertRecertification($due_date,$provider_id, $reminder_type, $reminder_sent_to, $name_reminder,$date_reminder_sent) {
        $db = $this->tableGateway->getAdapter();
        $sql = "INSERT INTO recertification(due_date, provider_id, reminder_type, reminder_sent_to, name_of_recipient, date_reminder_sent) VALUES ('".$due_date."',".$provider_id.",'".$reminder_type."','".$reminder_sent_to."','".$name_reminder."','".$date_reminder_sent."')";
        $db->getDriver()->getConnection()->execute($sql);
    }

    }
