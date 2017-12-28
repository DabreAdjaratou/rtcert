<?php

namespace Certification\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Certification\Model\CertificationMail;
use Certification\Form\CertificationMailForm;
use Zend\Session\Container;

class CertificationMailController extends AbstractActionController {

    protected $mailTable;

    public function getCertificationMailTable() {
        if (!$this->mailTable) {
            $sm = $this->getServiceLocator();
            $this->mailTable = $sm->get('Certification\Model\CertificationMailTable');
        }
        return $this->mailTable;
    }

    public function indexAction() {
        $this->forward()->dispatch('Certification\Controller\Certification', array('action' => 'index'));
        $email = base64_decode($this->params()->fromQuery(base64_encode('email'), null));
        $facility_in_charge_email = base64_decode($this->params()->fromQuery(base64_encode('facility_in_charge_email')), null);
        $certification_id = base64_decode($this->params()->fromQuery(base64_encode('certification_id')));
        $key = base64_decode($this->params()->fromQuery(base64_encode('key2'),null));
        /* provider name */
        $provider = base64_decode($this->params()->fromQuery(base64_encode('provider'), null));
        $due_date = base64_decode($this->params()->fromQuery(base64_encode('date_end_validity'), null));
        $provider_id = (int) base64_decode($this->params()->fromQuery(base64_encode('provider_id'), NULL));
//        die($provider_id);
        $form = new CertificationMailForm();
        $form->get('submit')->setValue('SEND EMAIL');
        $list = $this->getCertificationMailTable()->fetchAll();
        $request = $this->getRequest();
        $container = new Container('alert');

        if ($request->isPost()) {
            $save_mail = new CertificationMail();
            $form->setInputFilter($save_mail->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $save_mail->exchangeArray($form->getData());
                $mail = new \PHPMailer();  // create a new object
                $mail->IsSMTP(); // enable SMTP
                $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
                $mail->SMTPAuth = true;  // authentication enabled
                $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 465;
                $mail->Username = 'rtqiicertification@gmail.com';
                $mail->Password = 'rtqii@2017';
                $mail->SetFrom('rtqiicertification@gmail.com', 'RTQII PERSONNEL CERTIFICATION PROGRAM');
                $mail->addAddress($this->getRequest()->getpost('to_email', null));
                $mail->addCC($this->getRequest()->getpost('cc', null));
                $mail->addBCC($this->getRequest()->getpost('bcc', null));
                $mail->Subject = $this->getRequest()->getpost('subject', null);
                $mail->Body = $this->getRequest()->getpost('message', null);
                $type = $this->getRequest()->getpost('type', null);

                if (isset($_FILES['attachedfile'])) {
                    $mail->AddAttachment($_FILES['attachedfile']['tmp_name'], $_FILES['attachedfile']['name']);
                }

                if (!$mail->Send()) {
                    $error = 'Mail error: ' . $mail->ErrorInfo;
                    if ($error === 'Mail error: SMTP connect() failed. https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting') {
                        $container->alertMsg = 'MAIL ERROR:Sending the mail failed! Check your internet connection and try again.';
                    } else {
                        $container->alertMsg = $error;
                    }

                    return array('form' => $form, 'list' => $list);
                } else {
                    if ($type == 1 && !empty($certification_id)) {
                        $this->getCertificationMailTable()->dateCertificateSent($certification_id);
                    } elseif ($type == 2) {

                        $reminder_type = 'Email';
                        $reminder_sent_to = $this->getRequest()->getpost('type_recipient', null);
                        $name_reminder = $this->getRequest()->getpost('name_recipient', null);
                        $date_reminder_sent = date('Y-m-d');
                        if (!empty($certification_id)) {
                            $this->getCertificationMailTable()->insertRecertification($due_date, $provider_id, $reminder_type, $reminder_sent_to, $name_reminder, $date_reminder_sent);
                            $this->getCertificationMailTable()->reminderSent($certification_id);
                        }
                    }
                    $this->getCertificationMailTable()->saveCertificationMail($save_mail);
                    $container->alertMsg = 'Mail sent successfully';
                    return $this->redirect()->toRoute('certification-mail');
                }
            }
        }

        return array('form' => $form,
            'email' => $email,
            'list' => $list,
            'due_date' => $due_date,
            'provider' => $provider,
            'facility_in_charge_email' => $facility_in_charge_email,
            'key' => $key);
    }

}
