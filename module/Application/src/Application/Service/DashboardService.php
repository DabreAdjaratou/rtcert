<?php

namespace Application\Service;

use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class DashboardService {

    public $sm = null;

    public function __construct($sm = null) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function getQuickStats(){
        $certificationDb = $this->sm->get('Certification\Model\CertificationTable');
        return $certificationDb->getQuickStats();
    }

    public function getCertificationPieChartResults($params){
        $certificationDb = $this->sm->get('Certification\Model\CertificationTable');
        return $certificationDb->getCertificationPieChartResults($params);
    }
    
    public function getCertificationBarChartResults($params){
        $certificationDb = $this->sm->get('Certification\Model\CertificationTable');
        return $certificationDb->getCertificationBarChartResults($params);
    }

}