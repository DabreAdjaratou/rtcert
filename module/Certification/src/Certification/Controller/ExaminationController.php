<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ExaminationController extends AbstractActionController {

    protected $examinationTable;

    public function getExaminationTable() {
        if (!$this->examinationTable) {
            $sm = $this->getServiceLocator();
            $this->examinationTable = $sm->get('Certification\Model\ExaminationTable');
        }
        return $this->examinationTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        return new ViewModel(array(
            'examinations' => $this->getExaminationTable()->fetchAll(),
        ));
    }

}
