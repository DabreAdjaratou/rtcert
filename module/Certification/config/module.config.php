<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Certification;

return array(
    'router' => array(
        'routes' => array(
            'certification' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/certification[/:action][/:id][/:written][/:practical][/:direct][/:sample][/:last][/:first][/:middle][/:provider][/:certification_id][/:professional_reg_no][/:date_issued][/:key]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Certification',
                        'action' => 'index',
                    ),
                ),
            ),
            'recertification' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/recertification[/:action][/:recertification_id][/:certification_id][/:key]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'recertification_id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Recertification',
                        'action' => 'index',
                    ),
                ),
            ),
            'written-exam' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/written-exam[/:action][/:id_written_exam]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id_written_exam' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\WrittenExam',
                        'action' => 'index',
                    ),
                ),
            ),
            'practical-exam' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/practical-exam[/:action][/:practice_exam_id][/:id_written_exam]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'practice_exam_id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\PracticalExam',
                        'action' => 'index',
                    ),
                ),
            ),
            'examination' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/examination[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Examination',
                        'action' => 'index',
                    ),
                ),
            ),
            'provider' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/provider[/][:action][/:id][/:q]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Provider',
                        'action' => 'index',
                    ),
                ),
            ),
            'training' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/training[/:action][/:training_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'training_id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Training',
                        'action' => 'index',
                    ),
                ),
            ),
//            
            'training-organization' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/training-organization[/:action][/:training_organization_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'training_organization_id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\TrainingOrganization',
                        'action' => 'index',
                    ),
                ),
            ),
            'certification-mail' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/certification-mail[/:action][/:id][/:email][/:date_end_validity][/:provider][/:provider_id][/:facility_in_charge_email]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\CertificationMail',
                        'action' => 'index',
                    ),
                ),
            ),
            'region' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/region[/:action][/:id][/:azerty]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Region',
                        'action' => 'index',
                    ),
                ),
            ),
            'district' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/district[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\District',
                        'action' => 'index',
                    ),
                ),
            ),
//            
            'facility' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/certification-facility[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Certification\Controller\Facility',
                        'action' => 'index',
                    ),
                ),
            ),
            
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Certification\Controller\Certification' => "Certification\Controller\CertificationController",
            'Certification\Controller\CertificationIssuer' => "Certification\Controller\CertificationIssuerController",
            'Certification\Controller\PracticalExam' => "Certification\Controller\PracticalExamController",
            'Certification\Controller\Provider' => "Certification\Controller\ProviderController",
            'Certification\Controller\Recertification' => "Certification\Controller\RecertificationController",
            'Certification\Controller\TrainingCertificate' => "Certification\Controller\TrainingCertificateController",
            'Certification\Controller\Training' => "Certification\Controller\TrainingController",
            'Certification\Controller\TrainingOrganization' => "Certification\Controller\TrainingOrganizationController",
            'Certification\Controller\WrittenExam' => "Certification\Controller\WrittenExamController",
            'Certification\Controller\CertificationMail' => "Certification\Controller\CertificationMailController",
            'Certification\Controller\Examination' => "Certification\Controller\ExaminationController",
            'Certification\Controller\Region' => "Certification\Controller\RegionController",
            'Certification\Controller\District' => "Certification\Controller\DistrictController",
            'Certification\Controller\Facility' => "Certification\Controller\FacilityController",
           ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
