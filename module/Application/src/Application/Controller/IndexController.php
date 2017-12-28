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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $params = array();
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        $allSubmissions = $odkFormService->getAllApprovedSubmissions();        
        $testingVolume = $odkFormService->getAllApprovedTestingVolume('');        
  
        
        //$viewModel = new ViewModel();
        //$viewModel->setVariables(array('allSubmissions' => $allSubmissions,'testingVolume' => $testingVolume))
        //              ->setTerminal(true);
        //return $viewModel;
        return new ViewModel(array(
                                   'allSubmissions' => $allSubmissions,
                                   'testingVolume' => $testingVolume,
                                ));
    }
    
    public function auditLocationsAction()
    {
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        if($this->getRequest()->isPost()){
            $params=$this->getRequest()->getPost();
            $allSubmissions = $odkFormService->getAllApprovedSubmissionLocation($params);
            $viewModel = new ViewModel();
            $viewModel->setVariables(array('allSubmissions' => $allSubmissions))
                        ->setTerminal(true);
            return $viewModel;
        }
    }
    
    public function auditPerformanceAction()
    {
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        if($this->getRequest()->isPost()){
            $params=$this->getRequest()->getPost();
            $auditRoundWiseData=$odkFormService->getAuditRoundWiseData($params);
            $perf1 = $odkFormService->getPerformance($params);
            $viewModel = new ViewModel();
            $viewModel->setVariables(array('auditRoundWiseData' => $auditRoundWiseData,'perf1' => $perf1))
                      ->setTerminal(true);
            return $viewModel;
        }
    }
}
