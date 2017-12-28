<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Application\Model\SpiFormVer3Table;
use Application\Model\SpiFormVer3DuplicateTable;
use Application\Model\UsersTable;
use Application\Model\SpiFormLabelsTable;
use Application\Model\SpiRtFacilitiesTable;
use Application\Model\RolesTable;
use Application\Model\UserRoleMapTable;
use Application\Model\GlobalTable;
use Application\Model\EventLogTable;
use Application\Model\ResourcesTable;
use Application\Model\TempMailTable;
use Application\Model\UserTokenMapTable;
use Application\Model\AuditMailTable;
use Application\Model\SpiFormVer3DownloadTable;
use Application\Model\SpiFormVer3TempTable;

use Application\Service\OdkFormService;
use Application\Service\UserService;
use Application\Service\FacilityService;
use Application\Service\CommonService;
use Application\Service\RoleService;
use Application\Service\TcpdfExtends;

use Application\Model\Acl;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        if (php_sapi_name() != 'cli') {
            $eventManager->attach('dispatch', array($this, 'preSetter'), 100);
            //$eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'dispatchError'), -999);
        }        
        
    }
    
    
    public function preSetter(MvcEvent $e) {
		
		
		$session = new Container('credo');
		$sm = $e->getApplication()->getServiceManager();
		$commonService = $sm->get('CommonService');
		$config = $commonService->getGlobalConfigDetails();
		$session->countryName = $config['country-name'];
		
		
		
		
	if ($e->getRouteMatch()->getParam('controller') != 'Application\Controller\Login'
		&& $e->getRouteMatch()->getParam('controller') != 'Application\Controller\Index'
        && $e->getRouteMatch()->getParam('controller') != 'Application\Controller\Receiver'
        ) {
            
            if (!isset($session->userId) || $session->userId == "") {
				 if ($e->getRequest()->isXmlHttpRequest()) {
                return;
				} 
                $url = $e->getRouter()->assemble(array(), array('name' => 'login'));
                $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();

                // To avoid additional processing
                // we can attach a listener for Event Route with a high priority
                $stopCallBack = function($event) use ($response) {
                                    $event->stopPropagation();
                                    return $response;
                                };
                //Attach the "break" as a listener with a high priority
                $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);
                return $response;
            }
			else {
				$sm = $e->getApplication()->getServiceManager();
				$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
				$acl = $sm->get('AppAcl');
				$viewModel->acl = $acl;
				$session->acl = serialize($acl);
		
				$params = $e->getRouteMatch()->getParams();
				$resource = $params['controller'];
				$privilege = $params['action'];
				
				$role = $session->roleCode;
		
					//\Zend\Debug\Debug::dump($role);
					//\Zend\Debug\Debug::dump($resource);
					//\Zend\Debug\Debug::dump($acl->isAllowed($role, $resource, $privilege));
					//\Zend\Debug\Debug::dump($privilege);
					//die;
				 //if($e->getRequest()->isXmlHttpRequest() || $role == 'SA') {
				 if($e->getRequest()->isXmlHttpRequest()) {
				   return;
				 }else{
					 if (!$acl->hasResource($resource) || (!$acl->isAllowed($role, $resource, $privilege))) {
					 $e->setError('ACL_ACCESS_DENIED')->setParam('route', $e->getRouteMatch());
					 $e->getApplication()->getEventManager()->trigger('dispatch.error', $e);
					 }
				 }
			}
        }else{
			if(isset($session->userId)) {
				$sm = $e->getApplication()->getServiceManager();
				$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
				$acl = $sm->get('AppAcl');
				$viewModel->acl = $acl;
				$session->acl = serialize($acl);
			}
		}
    }
    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
		    'AppAcl' => function($sm) {
                    $resourcesTable = $sm->get('ResourcesTable');
                    $rolesTable = $sm->get('RolesTable');
                    return new Acl($resourcesTable->fetchAllResourceMap(), $rolesTable->fecthAllActiveRoles());
				},
                'SpiFormVer3Table' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpiFormVer3Table($dbAdapter);
                    return $table;
                },
                'UsersTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UsersTable($dbAdapter);
                    return $table;
                },
                'SpiFormLabelsTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpiFormLabelsTable($dbAdapter);
                    return $table;
                },
		'SpiRtFacilitiesTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpiRtFacilitiesTable($dbAdapter);
                    return $table;
                },
		'RolesTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new RolesTable($dbAdapter);
                    return $table;
                },
		'UserRoleMapTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserRoleMapTable($dbAdapter);
                    return $table;
                },
		'GlobalTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new GlobalTable($dbAdapter);
                    return $table;
                },
		'EventLogTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new EventLogTable($dbAdapter);
                    return $table;
                },
		'ResourcesTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new ResourcesTable($dbAdapter);
                    return $table;
                },
		'TempMailTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new TempMailTable($dbAdapter);
                    return $table;
                },'UserTokenMapTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTokenMapTable($dbAdapter);
                    return $table;
                },'AuditMailTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new AuditMailTable($dbAdapter);
                    return $table;
                },'SpiFormVer3DownloadTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpiFormVer3DownloadTable($dbAdapter);
                    return $table;
                },'SpiFormVer3DuplicateTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpiFormVer3DuplicateTable($dbAdapter);
                    return $table;
                },'SpiFormVer3TempTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new SpiFormVer3TempTable($dbAdapter);
                    return $table;
                },
		
                'OdkFormService' => function($sm) {
                    return new OdkFormService($sm);
                },
                'CommonService' => function($sm) {
                    return new CommonService($sm);
                },
                'UserService' => function($sm) {
                    return new UserService($sm);
                },
		'FacilityService' => function($sm) {
                    return new FacilityService($sm);
                },
		'CommonService' => function($sm) {
                    return new CommonService($sm);
                },
		'RoleService' => function($sm) {
                    return new RoleService($sm);
                },
		'TcpdfExtends' => function($sm) {
                    return new TcpdfExtends($sm);
                }
            ),
          
        );
    }
    
	
    public function getViewHelperConfig(){
        return array(
           'invokables' => array(
              'humanDateFormat' => 'Application\View\Helper\HumanDateFormat',
              'GlobalConfigHelper' => 'Application\View\Helper\GlobalConfigHelper',
           ),
        );
    }
    

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
