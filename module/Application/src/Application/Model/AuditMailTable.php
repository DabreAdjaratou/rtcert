<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Countries
 *
 * @author amit
 */
class AuditMailTable extends AbstractTableGateway {

    protected $table = 'audit_mails';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function insertAuditMailDetails($toEmailAddress,$cc,$subject,$message,$fromName,$fromEmailAddress){
        $data = array(
            'from_full_name' => $fromName,
            'from_mail' => $fromEmailAddress,
            'to_email' => $toEmailAddress,
            'cc' => $cc,
            'subject' => $subject,
            'message' => $message
        );
        $this->insert($data);
        return $this->lastInsertValue;
    }
    
    public function updateInitialAuditMailStatus($id){
        return $this->update(array('status'=>'not-sent'),array('mail_id'=>$id));
    }
    
    public function updateAuditMailStatus($id){
        return $this->update(array('status'=>'sent'),array('mail_id'=>$id));
    }
}