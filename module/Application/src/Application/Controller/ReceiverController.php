<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ReceiverController extends AbstractActionController
{

    public function indexAction()
    {        
        $viewModel = new ViewModel();
        
        //$this->var_error_log(file_get_contents('php://input'));
        
        $jsonData = utf8_encode(file_get_contents('php://input'));
        
        //$this->var_error_log($jsonData);
        
        $params = json_decode($jsonData,true);
        
        //$this->var_error_log($params);
        
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        $result = $odkFormService->saveSpiFormVer3($params);
        
        $viewModel->setTerminal(true);
        return $viewModel;        
    }

    
    public function var_error_log( $object=null ){
        ob_start();
        var_dump( $object );
        $contents = ob_get_contents();
        ob_end_clean();
        error_log( $contents );
    }
 
}

