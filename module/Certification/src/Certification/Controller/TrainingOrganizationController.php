<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Certification\Model\TrainingOrganization;
use Certification\Form\TrainingOrganizationForm;
use Zend\Session\Container;

class TrainingOrganizationController extends AbstractActionController {

    protected $TrainingOrganizationTable;

    public function getTrainingOrganizationTable() {
        if (!$this->TrainingOrganizationTable) {
            $sm = $this->getServiceLocator();
            $this->TrainingOrganizationTable = $sm->get('Certification\Model\TrainingOrganizationTable');
        }
        return $this->TrainingOrganizationTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $paginator = $this->getTrainingOrganizationTable()->fetchAll();
        return new ViewModel(array(
            'paginator' => $paginator,
        ));
    }

    public function addAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $form = new TrainingOrganizationForm();
        $form->get('submit')->setValue('SUBMIT');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $training_organization = new TrainingOrganization();
            $form->setInputFilter($training_organization->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $training_organization->exchangeArray($form->getData());
                $this->getTrainingOrganizationTable()->saveTraining_Organization($training_organization);
                $container = new Container('alert');
                $container->alertMsg = 'Training Organization added successfully';

                return $this->redirect()->toRoute('training-organization');
            }
        }
        return array('form' => $form);
    }

    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $training_organization_id = (int) base64_decode($this->params()->fromRoute('training_organization_id', 0));

        if (!$training_organization_id) {
            return $this->redirect()->toRoute('training-organization', array('action' => 'add'));
        }

        try {
            $training_organization = $this->getTrainingOrganizationTable()->getTraining_organization($training_organization_id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('training-organization', array('action' => 'index'));
        }

        $form = new TrainingOrganizationForm();
        $form->bind($training_organization);
        $form->get('submit')->setAttribute('value', 'UPDATE');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($training_organization->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getTrainingOrganizationTable()->saveTraining_Organization($training_organization);
                $container = new Container('alert');
                $container->alertMsg = 'Training Organization updated successfully';
                return $this->redirect()->toRoute('training-organization');
            }
        }

        return array(
            'training_organization_id' => $training_organization_id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $training_organization_id = (int) $this->params()->fromRoute('training_organization_id', 0);

        if (!$training_organization_id) {
            return $this->redirect()->toRoute('training-organization');
        } else {
            $forein_key = $this->getTrainingOrganizationTable()->foreigne_key($training_organization_id);
            if ($forein_key == 0) {
                $this->getTrainingOrganizationTable()->deleteOrganization($training_organization_id);
                $container = new Container('alert');
                $container->alertMsg = 'Deleted successfully';
                return $this->redirect()->toRoute('training-organization');
            } else {
                $container = new Container('alert');
                $container->alertMsg = 'Unable to delete this organization because it is used for one or more training (s).';
                return $this->redirect()->toRoute('training-organization');
            }
        }
    }

    

}
