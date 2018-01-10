<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model;

use Zend\Config\Factory;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Description of Acl
 *
 * @author amit
 */
class Acl extends ZendAcl {

    public function __construct($resourceList,$rolesList) {
        foreach ($resourceList as $res) {
            if (!$this->hasResource($res['resource_id'])) {
                $this->addResource(new GenericResource($res['resource_id']));
            }
        }

        foreach ($rolesList as $rol) {
            if (!$this->hasRole($rol['role_code'])) {
                $this->addRole(new GenericRole($rol['role_code']));
            }
        }

        $config = Factory::fromFile(CONFIG_PATH . DIRECTORY_SEPARATOR . "acl.config.php");

        foreach ($config as $role => $resources) {
            if (!$this->hasRole($role)) {
                $this->addRole(new GenericRole($role));
            }
            foreach ($resources as $resource => $permissions) {
                // $resource = stripcslashes($resource);
                foreach ($permissions as $privilege => $permission) {
                    $this->$permission($role, $resource, $privilege);
                }
            }
        }

        if (!$this->hasRole('daemon')) {
            $this->addRole('daemon');
        }

        $this->allow('daemon');
    }

}
