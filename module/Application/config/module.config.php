<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'home' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[/:action][/][:id]',
                    'defaults' => array(
                        'controller'    => 'Application\Controller\Index',
                        'action'        => 'index',
                    ),
                ),
                
            ),
            'homeAuditPerformance' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/audit-performance',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'audit-performance',
                    ),
                ),
            ),
            'homeAuditLocations' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/audit-locations',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'audit-locations',
                    ),
                ),
            ),
            'odk-receiver' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/receiver[/]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Receiver',
                        'action' => 'index',
                    ),
                ),
            ),            
            'login' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/login[/:action]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Login',
                        'action' => 'index',
                    ),
                ),
            ),     
            'spi-v3-form' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/spi-v3[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\SpiV3',
                        'action' => 'index',
                    ),
                ),
            ),
            'spi-facility' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/facility[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Facility',
                        'action' => 'index',
                    ),
                ),
            ),
            'spi-v3-reports' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/spi-v3-reports[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\SpiV3Reports',
                        'action' => 'index',
                    ),
                ),
            ),
            'common' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/common[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Common',
                        'action' => 'index',
                    ),
                ),
            ),
            'roles' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/roles[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Roles',
                        'action' => 'index',
                    ),
                ),
            ),
            'users' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/users[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Users',
                        'action' => 'index',
                    ),
                ),
            ),
            'config' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/config[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Config',
                        'action' => 'index',
                    ),
                ),
            ),
            'email' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/email[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Email',
                        'action' => 'index',
                    ),
                ),
            ),
            'dashboard' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/dashboard[/:action][/][:id]',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Dashboard',
                        'action' => 'index',
                    ),
                ),
            ),
            'view-data' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/view-data',
                    'defaults' => array(
                        'controller' => 'Application\Controller\SpiV3',
                        'action' => 'view-data',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => "Application\Controller\IndexController",
            'Application\Controller\Receiver' => "Application\Controller\ReceiverController",
            'Application\Controller\SpiV3' => "Application\Controller\SpiV3Controller",
            'Application\Controller\Login' => "Application\Controller\LoginController",
            'Application\Controller\Facility' => "Application\Controller\FacilityController",
            'Application\Controller\Roles' => "Application\Controller\RolesController",
            'Application\Controller\Common' => "Application\Controller\CommonController",
            'Application\Controller\Users' => "Application\Controller\UsersController",
            'Application\Controller\Config' => "Application\Controller\ConfigController",
            'Application\Controller\Email' => "Application\Controller\EmailController",
            'Application\Controller\Cron' => "Application\Controller\CronController",
            'Application\Controller\SpiV3Reports' => "Application\Controller\SpiV3ReportsController",
            'Application\Controller\Dashboard' => "Application\Controller\DashboardController",
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
       'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'mail-console-route' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'send-mail',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                            'action' => 'send-mail'
                        ),
                    ),
                ),
                'db-backup-console-route' => array(
                    'type'    => 'simple',
                    'options' => array(
                        'route'    => 'db-backup',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                             'action' => 'db-backup'
                        ),
                    ),
                ),
                'audit-mail-console-route' => array(
                    'type'    => 'simple',
                    'options' => array(
                        'route'    => 'send-audit-mail',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                             'action' => 'send-audit-mail'
                        ),
                    ),
                ),
                'generate-bulk-pdf' => array(
                    'type'    => 'simple',
                    'options' => array(
                        'route'    => 'generate-bulk-pdf',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Cron',
                             'action' => 'generate-bulk-pdf'
                        ),
                    ),
                ),
            ),
        ),
    ),
);
