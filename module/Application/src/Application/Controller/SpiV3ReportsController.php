<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class SpiV3ReportsController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function facilityReportAction()
    {
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $param = $request->getPost();
            $result = $odkFormService->getAllApprovedSubmissionsTable($param);
            return $this->getResponse()->setContent(Json::encode($result));
        }
        $spiV3auditRoundNo = $odkFormService->getSpiV3FormAuditNo();
        //$odkFormService = $this->getServiceLocator()->get('OdkFormService');
        //$allSubmissions = $odkFormService->getAllApprovedSubmissions();        
        //$rawSubmissions = $odkFormService->getAllSubmissions();
        $pendingCount = $odkFormService->getSpiV3PendingCount();
        $levelNamesResult=$odkFormService->getSpiV3FormUniqueLevelNames();
        //$spiV3auditRoundNo = $odkFormService->getSpiV3FormAuditNo();
        //
        return new ViewModel(array('pendingCount' => $pendingCount,'spiV3auditRoundNo'=>$spiV3auditRoundNo,
            'levelNamesResult'=>$levelNamesResult));
    }
    
    public function exportFacilityReportAction()
    {
        $request = $this->getRequest();                
        if ($request->isPost()) {
           $params = $request->getPost();
           $odkFormService = $this->getServiceLocator()->get('OdkFormService');
           $result= $odkFormService->exportFacilityReport($params);
           $viewModel = new ViewModel(array('result' => $result));
           $viewModel->setTerminal(true);
           return $viewModel;
       }
    }


}

