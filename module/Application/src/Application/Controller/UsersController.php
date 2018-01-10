<?php

namespace Application\Controller;

use Zend\Config\Config;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UsersController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userSerive = $this->getServiceLocator()->get('UserService');
            $result = $userSerive->getAllUsers($params);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $userSerive = $this->getServiceLocator()->get('UserService');
            $userSerive->addUser($params);
            return $this->redirect()->toRoute("users");
        }
        $roleSerive = $this->getServiceLocator()->get('RoleService');
        $commonSerive = $this->getServiceLocator()->get('CommonService');
        //$odkFormSerive = $this->getServiceLocator()->get('OdkFormService');
        $roleResult = $roleSerive->getAllActiveRoles();
        $countryResult = $commonSerive->getAllActiveCountries();
        $provinceResult = $commonSerive->getAllProvinces();
        $districtResult = $commonSerive->getAllDistricts();
        //$tokenResult = $odkFormSerive->getSpiV3FormUniqueTokens();
        return new ViewModel(
                            array(
                                  'roleResults' => $roleResult,
                                  'countryResult'=>$countryResult,
                                  'provinceResult'=>$provinceResult,
                                  'districtResult'=>$districtResult
                                  )
                            );
    }

    public function editAction() {
        $request = $this->getRequest();
        $userSerive = $this->getServiceLocator()->get('UserService');
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $userSerive->updateUser($params);
            return $this->redirect()->toRoute("users");
        } else {
            $id = base64_decode($this->params()->fromRoute('id'));
            $roleSerive = $this->getServiceLocator()->get('RoleService');
            $commonSerive = $this->getServiceLocator()->get('CommonService');
            $result = $userSerive->getUser($id);
            $roleResult = $roleSerive->getAllActiveRoles();
            $countryResult = $commonSerive->getAllActiveCountries();
            $selectedCountries = array();
            if(isset($result['userCountries']) && count($result['userCountries']) >0){
                foreach($result['userCountries'] as $country){
                    $selectedCountries[] = $country['country_id'];
                }
            }else if(isset($result['selectedCountries']) && count($result['selectedCountries']) >0){
              $selectedCountries = $result['selectedCountries'];
            }
            $provinceResult = $commonSerive->getSelectedCountryProvinces($selectedCountries);
            $params = array();
            if(isset($result['userProvinces']) && count($result['userProvinces']) >0){
                    foreach($result['userProvinces'] as $province){
                       $params['province'][] = $province['location_id'];
                    }
            }else if(isset($result['selectedProvinces']) && count($result['selectedProvinces']) >0){
                $params['province'] = $result['selectedProvinces'];
            }
            $districtResult = $commonSerive->getProvinceDistricts($params);
            return new ViewModel(array(
                'result' => $result,
                'roleResults' => $roleResult,
                'countryResult'=>$countryResult,
                'provinceResult'=>$provinceResult,
                'districtResult'=>$districtResult
            ));
        }
    }

}
