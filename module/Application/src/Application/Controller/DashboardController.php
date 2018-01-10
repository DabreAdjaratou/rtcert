<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class DashboardController extends AbstractActionController
{
    public function indexAction(){

        
        return new ViewModel(array());
    }
    
    public function auditDetailsAction(){
        $request = $this->getRequest();
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        if ($request->isPost()) {
            $params = $request->getPost();
            $odkFormService = $this->getServiceLocator()->get('OdkFormService');
            $result = $odkFormService->getAllApprovedSubmissionsDetailsBasedOnAuditDate($params);
            return $this->getResponse()->setContent(Json::encode($result));
        } else {
            $assesmentOfAuditDate = base64_decode($this->params()->fromRoute('id'));
            return new ViewModel(array(
                'assesmentOfAuditDate' => $assesmentOfAuditDate,
            ));
        }
    }
}
