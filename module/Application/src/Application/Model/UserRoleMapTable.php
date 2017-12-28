<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\AbstractTableGateway;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Countries
 *
 * @author ilahir
 */
class UserRoleMapTable extends AbstractTableGateway {

    protected $table = 'user_role_map';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    
}
