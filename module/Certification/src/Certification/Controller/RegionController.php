<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Certification\Model\Region;
use Certification\Form\RegionForm;

class RegionController extends AbstractActionController {

    protected $regionTable;

    public function getRegionTable() {
        if (!$this->regionTable) {
            $sm = $this->getServiceLocator();
            $this->regionTable = $sm->get('Certification\Model\RegionTable');
        }
        return $this->regionTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new RegionForm($dbAdapter);
        $form->get('submit')->setValue('Submit');
        $commonSerive = $this->getServiceLocator()->get('CommonService');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $region = new Region();
            $form->setInputFilter($region->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $region->exchangeArray($form->getData());
                $commonSerive->saveRegion($region);
                return $this->redirect()->toRoute('region');
            }
        }

        return new ViewModel(array(
            'regions' => $commonSerive->getAllProvinces($selectedCountries = array()),
            'form' => $form,
        ));
    }

    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $commonSerive = $this->getServiceLocator()->get('CommonService');
        $id = (int) base64_decode($this->params()->fromRoute('id', 0));
        if (!$id) {
            return $this->redirect()->toRoute('region', array(
                        'action' => 'index'
            ));
        }
        try {
            $region = $commonSerive->getLocation($id);
//            die(print_r($region));
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('region', array(
                        'action' => 'index'
            ));
        }
        $form = new RegionForm($dbAdapter);
        $form->bind($region);
        $form->get('submit')->setAttribute('value', 'Update');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $region = new Region();
            $form->setInputFilter($region->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $region->exchangeArray($form->getData());
                $commonSerive->saveRegion($request->getPost());
                return $this->redirect()->toRoute('region');
            }
        }

        return array(
            'id' => $id,
            'form' => $form
        );
    }

    public function deleteAction() {
        $commonSerive = $this->getServiceLocator()->get('CommonService');
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('region');
        } else {
            $forein_key = $this->getRegionTable()->foreigne_key($id);
            if ($forein_key == 0) {
                $commonSerive->deleteLocation($id);
                $container = new Container('alert');
                $container->alertMsg = 'Deleted successfully';
                return $this->redirect()->toRoute('region');
            } else {
                $container = new Container('alert');
                $container->alertMsg = 'Unable to delete this region because it is used for one or more district(s).';
                return $this->redirect()->toRoute('region');
            }
        }
    }

}
