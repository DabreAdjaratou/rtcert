<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Certification\Model\Recertification;
use Certification\Form\RecertificationForm;
use Zend\Session\Container;

class RecertificationController extends AbstractActionController {

    protected $recertificationTable;

    public function getRecertificationTable() {
        if (!$this->recertificationTable) {
            $sm = $this->getServiceLocator();
            $this->recertificationTable = $sm->get('Certification\Model\RecertificationTable');
        }
        return $this->recertificationTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
//        $reminder = $this->getRecertificationTable()->fetchAll2();
        $certification_id = (int) base64_decode($this->params()->fromQuery(base64_encode('certification_id'), null));
        $key = base64_decode($this->params()->fromQuery(base64_encode('key'), null));
        if (!empty($certification_id) && !empty($key)) {
            $this->getRecertificationTable()->ReminderSent($certification_id);
            $container = new Container('alert');
            $container->alertMsg = 'Perform successfully';
            return $this->redirect()->toRoute('recertification', array(
                        'action' => 'add'), array('query' => array(base64_encode('certification_id') => base64_encode($certification_id))));
        } else {

            return new ViewModel(array(
                'recertifications' => $this->getRecertificationTable()->fetchAll(),
                'reminders' => $this->getRecertificationTable()->fetchAll2()
            ));
        }
    }

    public function addAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $certification_id = (int) base64_decode($this->params()->fromQuery(base64_encode('certification_id'), null));
        $form = new RecertificationForm($dbAdapter);
        $form->get('submit')->setValue('SUBMIT');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $recertification = new Recertification();
            $form->setInputFilter($recertification->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $recertification->exchangeArray($form->getData());
                $this->getRecertificationTable()->saveRecertification($recertification);
                $container = new Container('alert');
                $container->alertMsg = 'Re-certification added successfully';
                return $this->redirect()->toRoute('recertification', array('action' => 'add'));
            }
        }
        if (isset($certification_id)) {
            $provider = $this->getRecertificationTable()->certificationInfo($certification_id);
            return array('form' => $form,
                'provider' => $provider);
        } else {
            return array('form' => $form);
        }
    }

    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $recertification_id = (int) base64_decode($this->params()->fromRoute('recertification_id', 0));
        if (!$recertification_id) {
            return $this->redirect()->toRoute('recertification', array(
                        'action' => 'add'
            ));
        }

        try {
            $recertification = $this->getRecertificationTable()->getRecertification($recertification_id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('recertification', array(
                        'action' => 'index'
            ));
        }
        $recertification->due_date = date("d-m-Y", strtotime($recertification->due_date));
        if (isset($recertification->date_reminder_sent)) {
            $recertification->date_reminder_sent = date("d-m-Y", strtotime($recertification->date_reminder_sent));
        }
        $form = new RecertificationForm($dbAdapter);
        $form->bind($recertification);
        $form->get('submit')->setAttribute('value', 'UPDATE');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($recertification->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getRecertificationTable()->saveRecertification($recertification);
                $container = new Container('alert');
                $container->alertMsg = 'Re-certification updated successfully';
                return $this->redirect()->toRoute('recertification');
            }
        }

        return array(
            'recertification_id' => $recertification_id,
            'form' => $form,
        );
    }

    public function xlsAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $countries = $this->getRecertificationTable()->getAllActiveCountries();
        $form = new RecertificationForm($dbAdapter);
        $form->get('submit')->setValue('GET REPORT');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $decision = $request->getPost('decision');
            $typeHiv = $request->getPost('typeHIV');
            $jobTitle = $request->getPost('jobTitle');
            $country = $request->getPost('country');
            $region = $request->getPost('region');
            $district = $request->getPost('district');
            $facility = $request->getPost('facility');
            $due_date = $request->getPost('due_date');
            if (!empty($due_date)) {
                $array = explode(" ", $due_date);
                $startDate = date("Y-m-d", strtotime($array[0]));
                $endDate = date("Y-m-d", strtotime($array[2]));
            } else {
//                
                $startDate = "";
                $endDate = "";
            }
            $reminder_type = $request->getPost('reminder_type');
            $reminder_sent_to = $request->getPost('reminder_sent_to');
            $date_reminder_sent = $request->getPost('date_reminder_sent');
            if (!empty($date_reminder_sent)) {
                $array2 = explode(" ", $date_reminder_sent);
                $startDate2 = date("Y-m-d", strtotime($array2[0]));
                $endDate2 = date("Y-m-d", strtotime($array2[2]));
            } else {
//                
                $startDate2 = "";
                $endDate2 = "";
            }
            $result = $this->getRecertificationTable()->report($startDate, $endDate, $decision, $typeHiv, $jobTitle, $country, $region, $district, $facility, $reminder_type, $reminder_sent_to, $startDate2, $endDate2);
            $objPHPExcel = new \PHPExcel();
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Verdana',
            ));


            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->setActiveSheetIndex()->mergeCells('A1:F1'); //merge some column
            $objPHPExcel->setActiveSheetIndex()->mergeCells('G1:N1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('Q1:S1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('T1:V1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('W1:AB1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('AC1:AP1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('AQ1:AV1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('AW1:AZ1');

            $objPHPExcel->getActiveSheet()->getStyle('A1:AZ2')->applyFromArray($styleArray); //apply style from array style array
            $objPHPExcel->getActiveSheet()->getStyle('A1:AZ2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK); // set cell border

            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(17); // row dimension
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25); // set default column dimension
            $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF8DC'); //column fill
            $objPHPExcel->getActiveSheet()->getStyle('G1:N2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6E6FA');
            $objPHPExcel->getActiveSheet()->getStyle('O1:P2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('A9A9A9');

            $objPHPExcel->getActiveSheet()->getStyle('Q1:S2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7FFFD4');
            $objPHPExcel->getActiveSheet()->getStyle('T1:V2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F5DEB3');
            $objPHPExcel->getActiveSheet()->getStyle('W1:AB2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F0E68C');
            $objPHPExcel->getActiveSheet()->getStyle('AC1:AP2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF5EE');
            $objPHPExcel->getActiveSheet()->getStyle('AQ1:AV2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F0FFFF');
            $objPHPExcel->getActiveSheet()->getStyle('AW1:AZ2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DDA0DD');


            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tester Identification');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Tester Contact Information');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Testing Site In charge');
            $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Facility In Charge');
            $objPHPExcel->getActiveSheet()->SetCellValue('W1', 'Practical Examination');
            $objPHPExcel->getActiveSheet()->SetCellValue('AC1', 'Facility In Charge');
            $objPHPExcel->getActiveSheet()->SetCellValue('AQ1', 'Certification');
            $objPHPExcel->getActiveSheet()->SetCellValue('AW1', 'Re-Certification');
//           

            $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Certification registration no');
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'Certification id');
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', 'Professional registration no');
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'Last name');
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', 'First name');
            $objPHPExcel->getActiveSheet()->SetCellValue('F2', 'Middle name');
            $objPHPExcel->getActiveSheet()->SetCellValue('G2', 'Country');
            $objPHPExcel->getActiveSheet()->SetCellValue('H2', 'Region');
            $objPHPExcel->getActiveSheet()->SetCellValue('I2', 'District');
            $objPHPExcel->getActiveSheet()->SetCellValue('J2', 'Type of vih test');
            $objPHPExcel->getActiveSheet()->SetCellValue('K2', 'Phone');
            $objPHPExcel->getActiveSheet()->SetCellValue('L2', 'Email');
            $objPHPExcel->getActiveSheet()->SetCellValue('M2', 'Prefered contact method');
            $objPHPExcel->getActiveSheet()->SetCellValue('N2', 'Facility Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('O2', 'Current job title');
            $objPHPExcel->getActiveSheet()->SetCellValue('P2', 'Time worked as tester');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q2', 'Testing site in charge name');
            $objPHPExcel->getActiveSheet()->SetCellValue('R2', 'Testing site in charge phone');
            $objPHPExcel->getActiveSheet()->SetCellValue('S2', 'Testing site in charge email');
            $objPHPExcel->getActiveSheet()->SetCellValue('T2', 'Facility in charge name');
            $objPHPExcel->getActiveSheet()->SetCellValue('U2', 'Facility in charge phone');
            $objPHPExcel->getActiveSheet()->SetCellValue('V2', 'Facility in charge email');
            $objPHPExcel->getActiveSheet()->SetCellValue('W2', 'Practical exam date');
            $objPHPExcel->getActiveSheet()->SetCellValue('X2', 'Practical exam admin');
            $objPHPExcel->getActiveSheet()->SetCellValue('Y2', 'Practical exam number of attempt');
            $objPHPExcel->getActiveSheet()->SetCellValue('Z2', 'Sample testing score');
            $objPHPExcel->getActiveSheet()->SetCellValue('AA2', 'Direct observation score');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB2', 'Practical exam score');
            $objPHPExcel->getActiveSheet()->SetCellValue('AC2', 'Written exam date');
            $objPHPExcel->getActiveSheet()->SetCellValue('AD2', 'Written exam admin');
            $objPHPExcel->getActiveSheet()->SetCellValue('AE2', 'Written exam number of attempt');
            $objPHPExcel->getActiveSheet()->SetCellValue('AF2', 'QA (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AG2', 'RT (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AH2', 'Safety (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AI2', 'Specimen collection (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AJ2', 'Testing algorithm (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AK2', 'Record keeping (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AL2', 'EQA/PT (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AM2', 'Ethics (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AN2', 'Inevntory (points)');
            $objPHPExcel->getActiveSheet()->SetCellValue('AO2', 'total point');
            $objPHPExcel->getActiveSheet()->SetCellValue('AP2', 'Written exam score');
            $objPHPExcel->getActiveSheet()->SetCellValue('AQ2', 'Final score');
            $objPHPExcel->getActiveSheet()->SetCellValue('AR2', 'Final decision');
            $objPHPExcel->getActiveSheet()->SetCellValue('AS2', 'Type of certification');
            $objPHPExcel->getActiveSheet()->SetCellValue('AT2', 'Date certificate issued');
            $objPHPExcel->getActiveSheet()->SetCellValue('AU2', 'certificate issuer');
            $objPHPExcel->getActiveSheet()->SetCellValue('AV2', 'Due date');
            $objPHPExcel->getActiveSheet()->SetCellValue('AW2', 'Type of reminder');
            $objPHPExcel->getActiveSheet()->SetCellValue('AX2', 'Reminder sent to');
            $objPHPExcel->getActiveSheet()->SetCellValue('AY2', 'Name of recipient');
            $objPHPExcel->getActiveSheet()->SetCellValue('AZ2', 'Date reminder sent');

            $ligne = 3;
            foreach ($result as $result) {
////           
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $ligne, $result['certification_reg_no']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $ligne, $result['certification_id']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $ligne, $result['professional_reg_no']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $ligne, $result['last_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $ligne, $result['first_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $ligne, $result['middle_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $ligne, (isset($result['country_name']))?$result['country_name']:'');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $ligne, (isset($result['region_name']))?$result['region_name']:'');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $ligne, (isset($result['district_name']))?$result['district_name']:'');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $ligne, $result['type_vih_test']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $ligne, $result['phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $ligne, $result['email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $ligne, $result['prefered_contact_method']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $ligne, $result['facility_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $ligne, $result['current_jod']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $ligne, $result['time_worked']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $ligne, $result['test_site_in_charge_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $ligne, $result['test_site_in_charge_phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $ligne, $result['test_site_in_charge_email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $ligne, $result['facility_in_charge_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $ligne, $result['facility_in_charge_phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(21, $ligne, $result['facility_in_charge_email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(22, $ligne, date("d-m-Y", strtotime($result['practical_exam_date'])));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(23, $ligne, $result['practical_exam_admin']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(24, $ligne, $result['practical_exam_type']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(25, $ligne, $result['direct_observation_score'] . ' %');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(26, $ligne, $result['Sample_testing_score'] . ' %');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(27, $ligne, $result['practical_total_score'] . ' %');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(28, $ligne, date("d-m-Y", strtotime($result['written_exam_date'])));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(29, $ligne, $result['written_exam_admin']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(30, $ligne, $result['written_exam_type']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(31, $ligne, $result['qa_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(32, $ligne, $result['rt_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(33, $ligne, $result['safety_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(34, $ligne, $result['specimen_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(35, $ligne, $result['testing_algo_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(36, $ligne, $result['report_keeping_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(37, $ligne, $result['EQA_PT_points']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(38, $ligne, $result['ethics_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(39, $ligne, $result['inventory_point']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(40, $ligne, $result['total_points']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(41, $ligne, $result['final_score'] . '  %');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(42, $ligne, ($result['practical_total_score'] + $result['final_score']) / 2 . '  %');
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(43, $ligne, $result['final_decision']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(44, $ligne, $result['certification_type']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(45, $ligne, date("d-m-Y", strtotime($result['date_certificate_issued'])));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(46, $ligne, $result['certification_issuer']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(47, $ligne, date("d-m-Y", strtotime($result['date_end_validity'])));
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(48, $ligne, $result['reminder_type']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(49, $ligne, $result['reminder_sent_to']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(50, $ligne, $result['name_of_recipient']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(51, $ligne, date("d-m-Y", strtotime($result['date_reminder_sent'])));
                $ligne++;
            }

            $objPHPExcel->getActiveSheet()->getStyle('A2:AV2')->getAlignment()->setWrapText(true); // make a new line in cell
            $objPHPExcel->getActiveSheet()->getStyle($objPHPExcel->getActiveSheet()->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //center column contain
            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . date('d-m-Y') . '_Recertification_report.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;
        }

        return array(
            'countries' => $countries,
            'form' => $form
        );
    }

}
