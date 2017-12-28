<?php

namespace Certification;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {
    
      public function getServiceConfig() {
        return array(
            'factories' => array(
                'Certification\Model\ProviderTable' => function($sm) {
                    $tableGateway = $sm->get('ProviderTableGateway');
                    $table = new \Certification\Model\ProviderTable($tableGateway);
                    return $table;
                },
                'ProviderTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Provider());
                    return new TableGateway('provider', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\TrainingOrganizationTable' => function($sm) {
                    $tableGateway = $sm->get('TrainingOrganizationTableGateway');
                    $table = new \Certification\Model\TrainingOrganizationTable($tableGateway);
                    return $table;
                },
                'TrainingOrganizationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\TrainingOrganization());
                    return new TableGateway('training_organization', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\TrainingTable' => function($sm) {
                    $tableGateway = $sm->get('TrainingTableGateway');
                    $table = new \Certification\Model\TrainingTable($tableGateway);
                    return $table;
                },
                'TrainingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Training());
                    return new TableGateway('training', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\WrittenExamTable' => function($sm) {
                    $tableGateway = $sm->get('WrittenExamTableGateway');
                    $table = new \Certification\Model\WrittenExamTable($tableGateway);
                    return $table;
                },
                'WrittenExamTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\WrittenExam());
                    return new TableGateway('written_exam', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\PracticalExamTable' => function($sm) {
                    $tableGateway = $sm->get('PracticalExamTableGateway');
                    $table = new \Certification\Model\PracticalExamTable($tableGateway);
                    return $table;
                },
                'PracticalExamTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\PracticalExam());
                    return new TableGateway('practical_exam', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\CertificationTable' => function($sm) {
                    $tableGateway = $sm->get('CertificationTableGateway');
                    $table = new \Certification\Model\CertificationTable($tableGateway);
                    return $table;
                },
                'CertificationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Certification());
                    return new TableGateway('certification', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\RecertificationTable' => function($sm) {
                    $tableGateway = $sm->get('RecertificationTableGateway');
                    $table = new \Certification\Model\RecertificationTable($tableGateway);
                    return $table;
                },
                'RecertificationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Recertification());
                    return new TableGateway('recertification', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\ExaminationTable' => function($sm) {
                    $tableGateway = $sm->get('ExaminationTableGateway');
                    $table = new \Certification\Model\ExaminationTable($tableGateway);
                    return $table;
                },
                'ExaminationTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Examination());
                    return new TableGateway('examination', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\CertificationMailTable' => function($sm) {
                    $tableGateway = $sm->get('CertificationMailTableGateway');
                    $table = new \Certification\Model\CertificationMailTable($tableGateway);
                    return $table;
                },
                'CertificationMailTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\CertificationMail());
                    return new TableGateway('certification_mail', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\RegionTable' => function($sm) {
                    $tableGateway = $sm->get('RegionTableGateway');
                    $table = new \Certification\Model\RegionTable($tableGateway);
                    return $table;
                },
                'RegionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Region());
                    return new TableGateway('certification_regions', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\DistrictTable' => function($sm) {
                    $tableGateway = $sm->get('DistrictTableGateway');
                    $table = new \Certification\Model\DistrictTable($tableGateway);
                    return $table;
                },
                'DistrictTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\District());
                    return new TableGateway('certification_districts', $dbAdapter, null, $resultSetPrototype);
                },
                'Certification\Model\FacilityTable' => function($sm) {
                    $tableGateway = $sm->get('FacilityTableGateway');
                    $table = new \Certification\Model\FacilityTable($tableGateway);
                    return $table;
                },
                'FacilityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Facility());
                    return new TableGateway('certification_facilities', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

}
