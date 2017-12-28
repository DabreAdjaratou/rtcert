<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;
use Application\Model\UserRoleMapTable;
use Application\Model\UserTokenMapTable;

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
class UsersTable extends AbstractTableGateway {

    protected $table = 'users';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    
    public function login($params) {
        $container = new Container('alert');
        $logincontainer = new Container('credo');
        $username = $params['username'];
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $password = sha1($params['password'] . $configResult["password"]["salt"]);        
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('u' => 'users'))
                                ->join(array('urm' => 'user_role_map'), 'urm.user_id=u.id', array('role_id'))
                                ->join(array('r' => 'roles'), 'r.role_id=urm.role_id', array('role_name','role_code'))
                                ->where(array('login' => $username, 'password' => $password,'u.status' =>'active'));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $sResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        if ($sResult) {
            $token = array();
            $userTokenQuery = $sql->select()->from(array('u_t_map' => 'user_token_map'))
                                            ->columns(array('token'))
                                            ->where(array('user_id'=>$sResult->id))
                                            ->order("token ASC");
            $userTokenQueryStr = $sql->getSqlStringForSqlObject($userTokenQuery);
            $userTokenResult = $dbAdapter->query($userTokenQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            foreach($userTokenResult as $userToken){
                $token[] = $userToken['token'];
            }
            $logincontainer->userId = $sResult->id;
            $logincontainer->login = $sResult->login;
            $logincontainer->roleCode = $sResult->role_code;
            $logincontainer->token = $token;
            return 'dashboard';
        } else {
            $container->alertMsg = 'Please check your login credentials';
            return 'login';
        }
    }
    
    public function addUserDetails($params){
        $common=new CommonService();
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $userRoleMap = new UserRoleMapTable($dbAdapter);
        $userTokenMap = new UserTokenMapTable($dbAdapter);
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $password = sha1($params['password'] . $configResult["password"]["salt"]);
        $lastInsertId = 0;
        if (isset($params['userName']) && trim($params['userName']) != "") {
            $data = array(
                'first_name' => $params['firstName'],
                'last_name' => $params['lastName'],
                'login' => $params['userName'],
                'password' => $password,
                'email' => $params['email'],
                'status' => $params['status'],
                'created_on'=>$common->getDateTime()
            );
            $this->insert($data);
            $lastInsertId=$this->lastInsertValue;
            if($lastInsertId>0){
                $userRoleMap->insert(array('user_id'=>$lastInsertId,'role_id'=>$params['roleId']));
                //Add User-Token
                if(isset($params['token']) && trim($params['token'])!= ''){
                    $splitToken = explode(",",$params['token']);
                    for($t=0;$t<count($splitToken); $t++){
                        $userTokenMap->insert(array('user_id'=>$lastInsertId,'token'=>trim($splitToken[$t])));
                    }
                }
            }
            return $lastInsertId;
        }
    }
    
    public function updateUserDetails($params){
        $common=new CommonService();
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $userRoleMap = new UserRoleMapTable($dbAdapter);
        $userTokenMap = new UserTokenMapTable($dbAdapter);
        $userId=base64_decode($params['userId']);
        if (isset($params['password']) && $params['password'] != '') {
            $config = new \Zend\Config\Reader\Ini();
            $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
            $password = sha1($params['password'] . $configResult["password"]["salt"]);
            $data = array('password' => $password);
            $this->update($data,array('id'=>$userId));
        }
        if (isset($params['userName']) && trim($params['userName']) != "") {
            $data = array(
                'first_name' => $params['firstName'],
                'last_name' => $params['lastName'],
                'login' => $params['userName'],
                'email' => $params['email'],
                'status' => $params['status']
            );
            $this->update($data,array('id'=>$userId));
            if($userId>0){
                $userRoleMap->update(array('role_id'=>$params['roleId']),array('user_id'=>$userId));
                //Update User-Token
                $userTokenMap->delete(array('user_id'=>$userId));
                if(isset($params['token']) && trim($params['token'])!= ''){
                    $splitToken = explode(",",$params['token']);
                    for($t=0;$t<count($splitToken); $t++){
                        $userTokenMap->insert(array('user_id'=>$userId,'token'=>trim($splitToken[$t])));
                    }
                }
            }
            return $userId;
        }
    }
    
    public function fetchAllUsers($parameters,$acl)
    {
        
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
        * you want to insert a non-database field (for example a counter or static image)
        */
	
        $aColumns = array('first_name','last_name','email','status');
        $orderColumns = array('first_name','email','status');

        /*
        * Paging
        */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
        * Ordering
        */

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
 
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
        * SQL queries
        * Get data to display
        */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from('users');
        
        
        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }
 
        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }
 
        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance 
        //echo $sQueryStr;die;
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);

        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $tQuery =  $sql->select()->from('users');
        $tQueryStr = $sql->getSqlStringForSqlObject($tQuery); // Get the string of the Sql, instead of the Select-instance
        $tResult = $dbAdapter->query($tQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        $iTotal = count($tResult);
        $output = array(
           "sEcho" => intval($parameters['sEcho']),
           "iTotalRecords" => $iTotal,
           "iTotalDisplayRecords" => $iFilteredTotal,
           "aaData" => array()
        );
        
        $loginContainer = new Container('credo');
        $role = $loginContainer->roleCode;
        if ($acl->isAllowed($role, 'Application\Controller\Users', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        
        foreach ($rResult as $aRow) {
         $row = array();
         
         $row[] = ucwords($aRow['first_name']." ".$aRow['last_name']);
         $row[] = $aRow['email'];
         $row[] = ucwords($aRow['status']);
         if($update){
         $edit = '<a href="/users/edit/'.base64_encode($aRow['id']).'" title="Edit"><i class="fa fa-pencil"></i> Edit</a>';
         $row[] =$edit;
         }
         $output['aaData'][] = $row;
        }
        return $output;
    }
    
    public function fetchUser($id){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from(array('u'=>'users'))
                               ->join(array('urm' => 'user_role_map'), "urm.user_id=u.id", array('role_id'),'left')
                               ->where(array('id'=>$id));
        $queryStr = $sql->getSqlStringForSqlObject($query);
        $queryResult = $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        if($queryResult){
            $userTokenQuery = $sql->select()->from(array('u_t_map' => 'user_token_map'))
                                            ->columns(array('token'))
                                            ->where(array('user_id'=>$id))
                                            ->order("token ASC");
            $userTokenQueryStr = $sql->getSqlStringForSqlObject($userTokenQuery);
            $queryResult['userToken'] = $dbAdapter->query($userTokenQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        }
        return $queryResult;
    }
}
