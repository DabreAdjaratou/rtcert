<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Certification\Model\District;
use Certification\Form\DistrictForm;
use Zend\Session\Container;

class DistrictController extends AbstractActionController {

    protected $districtTable;

    public function getDistrictTable() {
        if (!$this->districtTable) {
            $sm = $this->getServiceLocator();
            $this->districtTable = $sm->get('Certification\Model\DistrictTable');
        }
        return $this->districtTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new DistrictForm($dbAdapter);
        $form->get('submit')->setValue('Submit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $district = new District();
            $form->setInputFilter($district->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $district->exchangeArray($form->getData());
                $this->getDistrictTable()->saveDistrict($district);
                $container = new Container('alert');
                $container->alertMsg = 'District added successfully';
                return $this->redirect()->toRoute('district');
            }
        }
        return new ViewModel(array(
            'districts' => $this->getDistrictTable()->fetchAll(),
            'form' => $form,
        ));
    }

    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $id = (int) base64_decode($this->params()->fromRoute('id', 0));
        if (!$id) {
            return $this->redirect()->toRoute('district', array(
                        'action' => 'index'
            ));
        }

        try {
            $district = $this->getDistrictTable()->getDistrict($id);
//             die(print_r($district));
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('district', array(
                        'action' => 'index'
            ));
        }

        $form = new DistrictForm($dbAdapter);
        $form->bind($district);
        $form->get('submit')->setAttribute('value', 'Updated');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($district->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getDistrictTable()->saveDistrict($district);
                $container = new Container('alert');
                $container->alertMsg = 'District updated successfully';
                return $this->redirect()->toRoute('district');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }
    
    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('district');
        } else {
            $forein_key = $this->getDistrictTable()->foreigne_key($id);
            if ($forein_key == 0) {
                $this->getDistrictTable()->deleteDistrict($id);
                $container = new Container('alert');
                $container->alertMsg = 'Deleted successfully';
                return $this->redirect()->toRoute('district');
            } else {
                $container = new Container('alert');
                $container->alertMsg = 'Unable to delete this district because it is used for one or more facility(ies).';
                return $this->redirect()->toRoute('district');
            }
        }
    }
}
