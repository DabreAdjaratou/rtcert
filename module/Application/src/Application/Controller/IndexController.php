<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

class IndexController extends AbstractActionController
{
    public function indexAction(){

        $dashService = $this->getServiceLocator()->get('DashboardService');
        $quickStats = $dashService->getQuickStats();
        return new ViewModel(array(
            'quickStats' => $quickStats,
        ));
    }
}
