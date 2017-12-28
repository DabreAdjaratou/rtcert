<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Certification\Form\WrittenExamForm;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class WrittenExamController extends AbstractActionController {

    protected $writtenExamTable;

    public function getWrittenExamTable() {
        if (!$this->writtenExamTable) {
            $sm = $this->getServiceLocator();
            $this->writtenExamTable = $sm->get('Certification\Model\WrittenExamTable');
        }
        return $this->writtenExamTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        return new ViewModel(array(
            'writtens' => $this->getWrittenExamTable()->fetchAll()
        ));
    }

    public function addAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $practical = (int) base64_decode($this->params()->fromQuery(base64_encode('practice_exam_id'), 0));
        $provider = $this->getWrittenExamTable()->getProviderName($practical);
        $form = new WrittenExamForm($dbAdapter);
        $form->get('submit')->setValue('SUBMIT');
        $container = new Container('alert');
        $request = $this->getRequest();
        if ($request->isPost()) {

            $writtenExam = new \Certification\Model\WrittenExam();
            $form->setInputFilter($writtenExam->getInputFilter());
            $form->setData($request->getPost());
            $practical = $request->getPost('practical', null);
            $provider_id = $this->getRequest()->getPost('provider_id');
            $exam_to_val = $this->getWrittenExamTable()->examToValidate($provider_id);
            if ($exam_to_val > 0) {
                $container->alertMsg = 'This tester has a review pending validation. you must first validate it in the Examination tab.';
                return $this->redirect()->toRoute('written-exam', array('action' => 'add'));
            }
            $written = $this->getWrittenExamTable()->counWritten($provider_id);

            $nb_days = $this->getWrittenExamTable()->numberOfDays($provider_id);
            if (isset($nb_days) && $nb_days <= 30) {

                $container->alertMsg = 'The last attempt of this tester was ' . $nb_days . ' day(s) ago. Please wait at lease ' . date("d-m-Y", strtotime(date("Y-m-d") . "  + " . (31 - $nb_days) . " day"));
                return array(
                    'form' => $form,);
            } else {

                if ($written == 0) {
                    if ($form->isValid() && empty($practical)) {
                        $writtenExam->exchangeArray($form->getData());
                        $this->getWrittenExamTable()->saveWrittenExam($writtenExam);
                        $last_id = $this->getWrittenExamTable()->last_id();
                        $this->getWrittenExamTable()->insertToExamination($last_id);
                        $container->alertMsg = 'Written exam added successfully';
                        return $this->redirect()->toRoute('written-exam', array('action' => 'add'));
                    } else if ($form->isValid() && !empty($practical)) {
                        $writtenExam->exchangeArray($form->getData());
                        $this->getWrittenExamTable()->saveWrittenExam($writtenExam);
                        $last_id = $this->getWrittenExamTable()->last_id();
                        $nombre2 = $this->getWrittenExamTable()->countPractical2($practical);
                        if ($nombre2 == 0) {
                            $this->getWrittenExamTable()->examination($last_id, $practical);
                        } else {
                            $this->getWrittenExamTable()->insertToExamination($last_id);
                        }
                        $container->alertMsg = 'written exam added successfully';
                        return $this->redirect()->toRoute('written-exam', array('action' => 'add'));
                    }
                } else {
                    $container->alertMsg = 'Impossible to add !!!! Because this tester has already taken a written exam, he is waiting to add the practical exam.';
                    return array('form' => $form);
                }
            }
        }
        $nombre = null;
        if (isset($provider['id'])) {
            $nombre = $this->getWrittenExamTable()->attemptNumber($provider['id']);
        }

        return array('form' => $form,
            'practical' => $practical,
            'nombre' => $nombre,
            'provider' => $provider,
        );
    }

    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));

        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $id_written_exam = (int) base64_decode($this->params()->fromRoute('id_written_exam', 0));
        if (!$id_written_exam) {
            return $this->redirect()->toRoute('written-exam', array(
                        'action' => 'add'
            ));
        }

        try {
            $writtenExam = $this->getWrittenExamTable()->getWrittenExam($id_written_exam);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('written-exam', array(
                        'action' => 'index'
            ));
        }
        $provider = $this->getWrittenExamTable()->getProviderName2($id_written_exam);
//        die(print_r($provider));

        $writtenExam->date = date("d-m-Y", strtotime($writtenExam->date));
        $form = new WrittenExamForm($dbAdapter);
        $form->bind($writtenExam);
        $form->get('submit')->setAttribute('value', 'UPDATE');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($writtenExam->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getWrittenExamTable()->saveWrittenExam($writtenExam);
                $container = new Container('alert');
                $container->alertMsg = 'Written exam updated successfully';

                return $this->redirect()->toRoute('written-exam');
            }
        }
        $attemptNumber = $this->getWrittenExamTable()->getExamType($id_written_exam);
        return array(
            'id_written_exam' => $id_written_exam,
            'form' => $form,
            'attemptNumber' => $attemptNumber,
            'provider_id' => $provider['id'],
            'provider_name' => $provider['name'],
        );
    }

    public function attemptAction() {
        $q = (int) $_GET['q'];
        $result = $this->getWrittenExamTable()->attemptNumber($q);
        return array(
            'result' => $result,
        );
    }

    public function deleteAction() {
        $id_written_exam = (int) base64_decode($this->params()->fromRoute('id_written_exam', 0));

        if (!$id_written_exam) {
            return $this->redirect()->toRoute('written-exam');
        } else {
            $nb_written = $this->getWrittenExamTable()->CountWritten($id_written_exam);
            if ($nb_written == 1) {
                $this->getWrittenExamTable()->deleteWritten($id_written_exam);
                $container = new Container('alert');
                $container->alertMsg = 'Deleted successfully';
                return $this->redirect()->toRoute('written-exam');
            } else {
                $container = new Container('alert');
                $container->alertMsg = 'This written exam can not be deleted because it is already used for another examination!';
                return $this->redirect()->toRoute('written-exam');
            }
        }
    }

}
