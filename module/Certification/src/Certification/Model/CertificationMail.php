<?php

namespace Certification\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CertificationMail {

    public $mail_id;
    public $to_email;
    public $cc;
    public $bcc;
    public $type;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->mail_id = (!empty($data['mail_id'])) ? $data['mail_id'] : null;
        $this->to_email = (!empty($data['to_email'])) ? $data['to_email'] : null;
        $this->cc = (!empty($data['cc'])) ? $data['cc'] : null;
        $this->bcc = (!empty($data['bcc'])) ? $data['bcc'] : null;
        $this->type = (!empty($data['type'])) ? $data['type'] : null;
        $this->mail_date = (!empty($data['mail_date'])) ? $data['mail_date'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'mail_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'type',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'to_email',
                'required' => FALSE,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'cc',
                'required' => FALSE,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'bcc',
                'required' => FALSE,
                'validators' => array(
                    array(
                        'name' => 'EmailAddress'
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'subject',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'message',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ));


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
