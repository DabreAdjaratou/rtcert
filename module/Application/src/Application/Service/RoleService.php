<?php

namespace Application\Service;

use Zend\Session\Container;

class RoleService {

    public $sm = null;

    public function __construct($sm) {
        $this->sm = $sm;
    }

    public function getServiceManager() {
        return $this->sm;
    }

    public function addRoles($params) {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            
            $rolesDb = $this->sm->get('RolesTable');
            $rolesResult = $rolesDb->addRolesDetails($params);
            if ($rolesResult > 0) {
                $rolesDb->mapRolesPrivileges($params);
                $adapter->commit();
                //<-- Event log
                $subject = $rolesResult;
                $eventType = 'role-add';
                $action = 'added a new role '.$params['roleName'];
                $resourceName = 'Roles';
                $eventLogDb = $this->sm->get('EventLogTable');
                $eventLogDb->addEventLog($subject,$eventType,$action,$resourceName);
                //-------->
                $container = new Container('alert');
                $container->alertMsg = 'Roles added successfully';
            }
        } catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function updateRoles($params) {
        $adapter = $this->sm->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection();
        $adapter->beginTransaction();
        try {
            $rolesDb = $this->sm->get('RolesTable');
            $rolesResult = $rolesDb->updateRolesDetails($params);
            if ($rolesResult > 0) {
                $rolesDb->mapRolesPrivileges($params);
                $adapter->commit();
                $subject = $rolesResult;
                //<-- Event log
                $eventType = 'role-update';
                $action = 'updated a role '.$params['roleName'];
                $resourceName = 'Roles';
                $eventLogDb = $this->sm->get('EventLogTable');
                $eventLogDb->addEventLog($subject,$eventType,$action,$resourceName);
                //-------->
                $container = new Container('alert');
                $container->alertMsg = 'Roles updated successfully';
            }
            
        } catch (Exception $exc) {
            error_log($exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }
    
    public function getAllRolesDetails($params) {
        $rolesDb = $this->sm->get('RolesTable');
        $acl = $this->sm->get('AppAcl');
        return $rolesDb->fetchAllRoleDetails($params,$acl);
    }
    
    public function getRole($id) {
        $rolesDb = $this->sm->get('RolesTable');
        return $rolesDb->getRolesDetails($id);
    }
    
    public function getAllActiveRoles(){
        $rolesDb = $this->sm->get('RolesTable');
        return $rolesDb->fecthAllActiveRoles();
    }
    
    public function getAllRoles() {
        $rolesDb = $this->sm->get('RolesTable');
        return $rolesDb->fetchAllRoles();
    }
}

?>
