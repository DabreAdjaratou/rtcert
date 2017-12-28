<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Certification\Model\Training;
use Certification\Form\TrainingForm;
use Zend\Session\container;

class TrainingController extends AbstractActionController {

    protected $TrainingTable;

    public function getTrainingTable() {
        if (!$this->TrainingTable) {
            $sm = $this->getServiceLocator();
            $this->TrainingTable = $sm->get('Certification\Model\TrainingTable');
        }
        return $this->TrainingTable;
    }

    public function indexAction() {

        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        return new ViewModel(array(
            'trainings' => $this->getTrainingTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new TrainingForm($dbAdapter);
        $form->get('submit')->setValue('SUBMIT');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $training = new Training();
            $form->setInputFilter($training->getInputFilter());
            $select_time = $request->getPost('select_time');
//            die($select_time);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $training->exchangeArray($form->getData());
                $training->length_of_training = $training->length_of_training . ' ' . $select_time;
                $this->getTrainingTable()->saveTraining($training);
                $container = new Container('alert');
                $container->alertMsg = 'New training added successfully';

                return $this->redirect()->toRoute('training', array('action' => 'add'));
            }
        }

        return array('form' => $form);
    }

//
    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));

        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $training_id = (int) base64_decode($this->params()->fromRoute('training_id', 0));
        if (!$training_id) {
            return $this->redirect()->toRoute('training', array(
                        'action' => 'add'
            ));
        }

        try {
            $training = $this->getTrainingTable()->getTraining($training_id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('training', array(
                        'action' => 'index'
            ));
        }
        $training->last_training_date = date("d-m-Y", strtotime($training->last_training_date));
        if (isset($training->date_certificate_issued)) {
            $training->date_certificate_issued = date("d-m-Y", strtotime($training->date_certificate_issued));
        }

        $form = new TrainingForm($dbAdapter);

        $time_array = explode(' ', $training->length_of_training);
        $time1 = $time_array[0];
        $time2 = $time_array[1];


        $training->length_of_training = $time1;
        $form->bind($training);
        $form->get('submit')->setAttribute('value', 'UPDATE');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $select_time = $request->getPost('select_time');
            $form->setInputFilter($training->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $training->length_of_training = $training->length_of_training . ' ' . $select_time;
                $this->getTrainingTable()->saveTraining($training);
                $container = new Container('alert');
                $container->alertMsg = 'Training updated successfully';
                return $this->redirect()->toRoute('training');
            }
        }

        return array(
            'training_id' => $training_id,
            'form' => $form,
            'time2' => $time2,
        );
    }

    public function deleteAction() {
        $training_id = (int) $this->params()->fromRoute('training_id', 0);

        if (!$training_id) {
            return $this->redirect()->toRoute('training');
        } else {

            $this->getTrainingTable()->deleteTraining($training_id);
            $container = new Container('alert');
            $container->alertMsg = 'Deleted successfully';
            return $this->redirect()->toRoute('training');
        }
    }

    public function xlsAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new TrainingForm($dbAdapter);
        $form->get('submit')->setValue('GET REPORT');
        $regions = $this->getTrainingTable()->getRegions();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $type_of_competency = $request->getPost('type_of_competency');
            $type_of_training = $request->getPost('type_of_training');
            $training_organization_id = $request->getPost('training_organization_id');
            $training_certificate = $request->getPost('training_certificate');
            $typeHiv = $request->getPost('type_vih_test');
            $jobTitle = $request->getPost('current_jod');
            $region = $request->getPost('region');
            $district = $request->getPost('district');
            $facility = $request->getPost('facility_id');

            $training = $this->getTrainingTable()->report($type_of_competency, $type_of_training, $training_organization_id, $training_certificate, $typeHiv, $jobTitle, $region, $district, $facility);

            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->setActiveSheetIndex()->mergeCells('A1:F1'); //merge some column
            $objPHPExcel->setActiveSheetIndex()->mergeCells('G1:L1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('P1:R1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('S1:U1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('V1:AE1');

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tester Identification');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Tester Contact Information');
            $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Testing Site In charge');
            $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Facility In Charge');
            $objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Training');

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Verdana',
            ));
            $objPHPExcel->getActiveSheet()->getStyle('A1:AE2')->applyFromArray($styleArray); //apply style from array style array
            $objPHPExcel->getActiveSheet()->getStyle('A1:AE2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK); // set cell border

            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(17); // row dimension
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF8DC'); //column fill
            $objPHPExcel->getActiveSheet()->getStyle('G1:L2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6E6FA');
            $objPHPExcel->getActiveSheet()->getStyle('M1:M2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F5DEB3');
            $objPHPExcel->getActiveSheet()->getStyle('P1:R2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('A9A9A9');
            $objPHPExcel->getActiveSheet()->getStyle('S1:U2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7FFFD4');
            $objPHPExcel->getActiveSheet()->getStyle('V1:AE2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F5F5DC');


            $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Certification registration no');
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Certification id');
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Professional registration no');
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Last name');
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', 'First name');
            $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Middle name');
            $objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Region');
            $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'District');
            $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'Type of vih test');
            $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Phone');
            $objPHPExcel->getActiveSheet()->SetCellValue('K2', 'Email');
            $objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Prefered contact method');
            $objPHPExcel->getActiveSheet()->SetCellValue('M2', 'facility');
            $objPHPExcel->getActiveSheet()->SetCellValue('N2', 'Current job title');
            $objPHPExcel->getActiveSheet()->SetCellValue('O2', 'Time worked as tester');
            $objPHPExcel->getActiveSheet()->SetCellValue('P2', 'Testing site in charge name');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q2', 'Testing site in charge phone');
            $objPHPExcel->getActiveSheet()->SetCellValue('R2', 'Testing site in charge email');
            $objPHPExcel->getActiveSheet()->SetCellValue('S2', 'Facility in charge name');
            $objPHPExcel->getActiveSheet()->SetCellValue('T2', 'Facility in charge phone');
            $objPHPExcel->getActiveSheet()->SetCellValue('U2', 'Facility in charge email');
            $objPHPExcel->getActiveSheet()->SetCellValue('V2', 'Type of competency');
            $objPHPExcel->getActiveSheet()->SetCellValue('W2', 'Date of last training/activity');
            $objPHPExcel->getActiveSheet()->SetCellValue('X2', 'Type of activity/training');
            $objPHPExcel->getActiveSheet()->SetCellValue('Y2', 'Length of training/activity');
            $objPHPExcel->getActiveSheet()->SetCellValue('Z2', 'Training organization');
            $objPHPExcel->getActiveSheet()->SetCellValue('AA2', 'Type of training organization');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB2', 'Name of facilitator(s)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AC2', 'Training certificate (if available)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AD2', 'Date certificate issued (if available)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AE2', 'Comments');

            $ligne = 3;
            foreach ($training as $training) {
//           
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $ligne, $training['certification_reg_no']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $ligne, $training['certification_id']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $ligne, $training['professional_reg_no']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $ligne, $training['last_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $ligne, $training['first_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $ligne, $training['middle_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $ligne, $training['region_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $ligne, $training['district_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $ligne, $training['type_vih_test']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $ligne, $training['phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $ligne, $training['email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $ligne, $training['prefered_contact_method']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $ligne, $training['facility_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $ligne, $training['current_jod']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $ligne, $training['time_worked']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $ligne, $training['test_site_in_charge_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $ligne, $training['test_site_in_charge_phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $ligne, $training['test_site_in_charge_email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $ligne, $training['facility_in_charge_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $ligne, $training['facility_in_charge_phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $ligne, $training['facility_in_charge_email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $ligne, $training['type_of_competency']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $ligne, date("d-m-Y", strtotime($training['last_training_date'])));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $ligne, $training['type_of_training']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $ligne, $training['length_of_training']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $ligne, $training['training_organization_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $ligne, $training['type_organization']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $ligne, $training['facilitator']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $ligne, $training['training_certificate']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $ligne, date("d-m-Y", strtotime($training['date_certificate_issued'])));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $ligne, $training['Comments']);
                $ligne++;
            }
            $objPHPExcel->getActiveSheet()->getStyle('A2:AE2')->getAlignment()->setWrapText(true); // make a new line in cell
            $objPHPExcel->getActiveSheet()->getStyle($objPHPExcel->getActiveSheet()->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //center column contain

            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . date('d-m-Y') . '_trainingReport.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;
        }
        return array('form' => $form,
            'regions' => $regions);
//    }
    }

}
