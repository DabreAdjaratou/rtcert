<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Expression;
use Zend\Session\Container;
use Application\Service\CommonService;
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
class EventLogTable extends AbstractTableGateway {

    protected $table = 'event_log';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function addEventLog($subject, $eventType, $action, $resourceName) {
            $logincontainer = new Container('credo');
            $actor_id = $logincontainer->userId;
            $common = new CommonService();
            $currentDateTime=$common->getDateTime();
            
            $data = array('actor'=>$actor_id,
                          'subject'=>$subject,
                          'event_type'=>$eventType,
                          'action'=>$action,
                          'resource_name'=>$resourceName,
                          'date_time'=> $currentDateTime
                        );
            $id = $this->insert($data);
    }
    
}
?>