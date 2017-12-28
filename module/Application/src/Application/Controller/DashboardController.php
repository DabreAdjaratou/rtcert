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
        $params = array();
        $odkFormService = $this->getServiceLocator()->get('OdkFormService');
        $perf1 = $odkFormService->getPerformance($params);
        $perflast30 = $odkFormService->getPerformanceLast30Days('');        
        $perflast180 = $odkFormService->getPerformanceLast180Days();        
        $allSubmissions = $odkFormService->getAllApprovedSubmissions();
        $testingVolume = $odkFormService->getAllApprovedTestingVolume('');      
        $rawSubmissions = $odkFormService->getAllSubmissions();        
        //$auditRoundWiseData = $odkFormService->getAuditRoundWiseData('');
        //$zeroCounts = $odkFormService->getZeroQuestionCounts();
        //$spiV3Labels = $odkFormService->getSpiV3FormLabels();
        $spiV3auditRoundNo = $odkFormService->getSpiV3FormAuditNo();
        $levelNamesResult=$odkFormService->getSpiV3FormUniqueLevelNames();
        $testingPointResult=$odkFormService->getAllTestingPointType();
        
        return new ViewModel(array('perf1' => $perf1,
                                   'perflast30' => $perflast30,
                                   'perflast180' => $perflast180,
                                   'allSubmissions' => $allSubmissions,
                                   'testingVolume' => $testingVolume,
                                   'rawSubmissions' => $rawSubmissions,
                                   //'auditRoundWiseData' => $auditRoundWiseData,
                                   //'spiV3Labels' => $spiV3Labels,
                                   //'zeroCounts' => $zeroCounts,
                                   'spiV3auditRoundNo'=>$spiV3auditRoundNo,
                                   'testingPointResult' => $testingPointResult,
                                    'levelNamesResult' => $levelNamesResult));
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
