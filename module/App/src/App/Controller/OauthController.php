<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use App\Services\GoogleApi;
use Zend\Debug\Debug;

class OauthController extends BaseController
{
    public function indexAction()
    {
        // Get the API client and construct the service object.
        $code = $this->params()->fromQuery('code', false);
        $googleApi = $this->getServiceLocator()->get('googleApi');
        if ($googleApi->authenticate($code)) {
            return $this->redirect()->toRoute('App/profile');
        }
    }
}
