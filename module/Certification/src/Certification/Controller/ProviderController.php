<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Certification\Model\Provider;
use Certification\Form\ProviderForm;
use Zend\Session\Container;

class ProviderController extends AbstractActionController {

    protected $providerTable;

    public function getProviderTable() {
        if (!$this->providerTable) {
            $sm = $this->getServiceLocator();
            $this->providerTable = $sm->get('Certification\Model\ProviderTable');
        }
        return $this->providerTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        return new ViewModel(array(
            'providers' => $this->getProviderTable()->fetchAll(),
        ));
    }

    public function addAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new ProviderForm($dbAdapter);
        $form->get('submit')->setValue('SUBMIT');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $provider = new Provider();
            $form->setInputFilter($provider->getInputFilter());
            $form->setData($request->getPost());
            $select_time = $request->getPost('select_time');

            if ($form->isValid()) {
                $provider->exchangeArray($form->getData());
                $provider->time_worked = $provider->time_worked . ' ' . $select_time;
                ?>
                <pre> <?php // print_r($provider)                                 ?></pre>

                <?php
                $this->getProviderTable()->saveProvider($provider);
                $container = new Container('alert');
                $container->alertMsg = 'New tester added successfully';
                return $this->redirect()->toRoute('provider', array('action' => 'add'));
            }
        }
        return array('form' => $form,
            'providers' => $this->getProviderTable()->fetchAll(),
        );
    }

    public function editAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $id = (int) base64_decode($this->params()->fromRoute('id', 0));

        if (!$id) {
            return $this->redirect()->toRoute('provider', array(
                        'action' => 'add'
            ));
        }

        try {
            $provider = $this->getProviderTable()->getProvider($id);
//            die(print_r($provider));
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('provider', array(
                        'action' => 'index'
            ));
        }

        $form = new ProviderForm($dbAdapter);
        $time_array = explode(' ', $provider->time_worked);
        $time1 = $time_array[0];
        $time2 = $time_array[1];

        $provider->time_worked = $time1;
        $form->bind($provider);
        $form->get('submit')->setAttribute('value', 'UPDATE');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $select_time = $request->getPost('select_time');

            $form->setInputFilter($provider->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $provider->time_worked = $provider->time_worked . ' ' . $select_time;
                $this->getProviderTable()->saveProvider($provider);
                $container = new Container('alert');
                $container->alertMsg = 'Tester updated successfully';
                return $this->redirect()->toRoute('provider');
            }
        }
        $district = $this->getProviderTable()->DistrictName($provider->district);
        $facility = $this->getProviderTable()->FacilityName($provider->facility_id);

        return array(
            'id' => $id,
            'form' => $form,
            'district_id' => $district['district_id'],
            'district_name' => $district['district_name'],
            'facility_id' => $facility['facility_id'],
            'facility_name' => $facility['facility_name'],
            'time2' => $time2,
        );
    }

    public function districtAction() {

        $q = (int) $_GET['q'];
        $result = $this->getProviderTable()->getDistrict($q);
        return array(
            'result' => $result,
        );
    }

    public function facilityAction() {

        $q = (int) $_GET['q'];
        $result = $this->getProviderTable()->getFacility($q);
        return array(
            'result' => $result,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('provider');
        } else {
            $keys = $this->getProviderTable()->foreigne_key($id);
            $key1 = $keys['nombre'];
            $key2 = $keys['nombre2'];
            $key3 = $keys['nombre3'];
            if ($key1 == 0 && $key2 == 0 && $key3 == 0) {
                $this->getProviderTable()->deleteProvider($id);
                $container = new Container('alert');
                $container->alertMsg = 'Deleted successfully';
                return $this->redirect()->toRoute('provider');
            } else {
                $container = new Container('alert');
                $container->alertMsg = 'Unable to remove this provider because he has already completed an exam or a training.';
                return $this->redirect()->toRoute('provider');
            }
        }
    }

    public function xlsAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new ProviderForm($dbAdapter);
        $form->get('submit')->setValue('GET REPORT');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $typeHiv = $request->getPost('type_vih_test');
            $jobTitle = $request->getPost('current_jod');
            $region = $request->getPost('region');
            $district = $request->getPost('district');
            $facility = $request->getPost('facility_id');
            $contact_method = $request->getPost('prefered_contact_method');
            $provider = $this->getProviderTable()->report($typeHiv, $jobTitle, $region, $district, $facility, $contact_method);

            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->setActiveSheetIndex()->mergeCells('A1:F1'); //merge some column
            $objPHPExcel->setActiveSheetIndex()->mergeCells('G1:L1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('P1:R1');
            $objPHPExcel->setActiveSheetIndex()->mergeCells('S1:U1');

            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tester Identification');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Tester Contact Information');
            $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Testing Site In charge');
            $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Facility In Charge');

            $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Verdana',
            ));
            $objPHPExcel->getActiveSheet()->getStyle('A1:U2')->applyFromArray($styleArray); //apply style from array style array
            $objPHPExcel->getActiveSheet()->getStyle('A1:U2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK); // set cell border

            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(17); // row dimension
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFF8DC'); //column fill
            $objPHPExcel->getActiveSheet()->getStyle('G1:L2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('E6E6FA');
            $objPHPExcel->getActiveSheet()->getStyle('M1:M2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F5DEB3');
            $objPHPExcel->getActiveSheet()->getStyle('P1:R2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('A9A9A9');
            $objPHPExcel->getActiveSheet()->getStyle('S1:U2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('7FFFD4');


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




            $ligne = 3;
            foreach ($provider as $provider) {
//           
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $ligne, $provider['certification_reg_no']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $ligne, $provider['certification_id']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $ligne, $provider['professional_reg_no']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $ligne, $provider['last_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $ligne, $provider['first_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $ligne, $provider['middle_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $ligne, $provider['region_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $ligne, $provider['district_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $ligne, $provider['type_vih_test']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $ligne, $provider['phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $ligne, $provider['email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $ligne, $provider['prefered_contact_method']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $ligne, $provider['facility_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $ligne, $provider['current_jod']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $ligne, $provider['time_worked']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $ligne, $provider['test_site_in_charge_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $ligne, $provider['test_site_in_charge_phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $ligne, $provider['test_site_in_charge_email']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(18, $ligne, $provider['facility_in_charge_name']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(19, $ligne, $provider['facility_in_charge_phone']);
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(20, $ligne, $provider['facility_in_charge_email']);
                $ligne++;
            }
            $objPHPExcel->getActiveSheet()->getStyle('A2:T2')->getAlignment()->setWrapText(true); // make a new line in cell
            $objPHPExcel->getActiveSheet()->getStyle($objPHPExcel->getActiveSheet()->calculateWorksheetDimension())->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //center column contain

            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . date('d-m-Y') . '_list of all testers.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit;
        }
        return array('form' => $form);
    }

}
