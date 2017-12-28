<?php

namespace Application\Controller;

use Zend\Config\Config;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FacilityController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->getAllFacilities($params);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->addFacility($params);
            return $this->redirect()->toRoute("spi-facility");
        }
    }
    
    public function editAction() {
        $request = $this->getRequest();
        $facilityService= $this->getServiceLocator()->get('FacilityService');
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $facilityService->updateFacility($params);
            return $this->redirect()->toRoute("spi-facility");
        } else {
            $id = base64_decode($this->params()->fromRoute('id'));
            $result = $facilityService->getFacility($id);
            //\Zend\Debug\Debug::dump($result);
            //die;
            return new ViewModel(array(
                'result' => $result,
            ));
        }
    }
    
    public function facilityListAction(){
        $request = $this->getRequest();
        
        if ($request->isGet()) {
            $val = $request->getQuery('search');
            //\Zend\Debug\Debug::dump($val);
        //die;
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->getFacilityList($val);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function getFacilityNameAction()
    {
        $layout = $this->layout();
        $layout->setTemplate('layout/modal');
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        $result = $odkFormService->getAllFacilityNames();
        return new ViewModel(array(
            'facilityName' => $result
        ));
    }
    
    public function getTestingPointAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->getAllTestingPoints($params);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function getFacilityAuditRoundAction(){
        $request = $this->getRequest();                
        if ($request->isPost()) {
            $params = $request->getPost();
            $odkFormService = $this->getServiceLocator()->get('OdkFormService');
            $result = $odkFormService->getSpiV3FormFacilityAuditNo($params);
            $viewModel = new ViewModel(array(
                'spiV3auditRoundNo'=>$result
                ));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }
    
    public function getProvinceListAction(){
        $layout = $this->layout();
        $layout->setTemplate('layout/modal');
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        $facilityService= $this->getServiceLocator()->get('FacilityService');
        $result = $odkFormService->getAllFacilityNames();
        $provinceResult = $facilityService->getProvinceList();
        return new ViewModel(array(
            'facilityName' => $result,
            'provinces' => $provinceResult
        ));
    }
    
    public function mapProvinceAction(){
        $request = $this->getRequest();                
        if ($request->isPost()) {
            $params = $request->getPost();
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->mapProvince($params);
            $viewModel = new ViewModel(array(
                'result'=>$result
                ));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }
    
    public function getFacilityDetailsAction(){
        $request = $this->getRequest();                
        if ($request->isPost()) {
            $params = $request->getPost();
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->getFacilityDetails($params);
            $viewModel = new ViewModel(array(
                'result'=>$result
                ));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }
    
    public function exportFacilityAction(){
        $request = $this->getRequest();                
        if ($request->isPost()) {
            $facilityService= $this->getServiceLocator()->get('FacilityService');
            $result = $facilityService->exportFacility();
            $viewModel = new ViewModel(array(
                'result'=>$result
                ));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }
    
    public function searchProvinceListAction()
    {
        $facilityService= $this->getServiceLocator()->get('FacilityService');
        $request = $this->getRequest();
        if ($request->isGet()) {
            $val = $request->getQuery('q');
            $result=$facilityService->getProvinceData($val);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    public function searchDistrictListAction()
    {
        $facilityService= $this->getServiceLocator()->get('FacilityService');
        $request = $this->getRequest();
        if ($request->isGet()) {
            $val = $request->getQuery('q');
            $result=$facilityService->getDistrictData($val);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
}
