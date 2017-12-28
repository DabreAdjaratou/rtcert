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
        $odkFormSerive = $this->getServiceLocator()->get('OdkFormService');
        $roleResult = $roleSerive->getAllActiveRoles();
        $tokenResult = $odkFormSerive->getSpiV3FormUniqueTokens();
        return new ViewModel(array('roleResults' => $roleResult,'tokenResults' => $tokenResult));
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
            $result = $userSerive->getUser($id);
            $roleSerive = $this->getServiceLocator()->get('RoleService');
            $odkFormSerive = $this->getServiceLocator()->get('OdkFormService');
            $roleResult = $roleSerive->getAllActiveRoles();
            $tokenResult = $odkFormSerive->getSpiV3FormUniqueTokens();
            return new ViewModel(array(
                'result' => $result,
                'roleResults' => $roleResult,
                'tokenResults' => $tokenResult
            ));
        }
    }

}
