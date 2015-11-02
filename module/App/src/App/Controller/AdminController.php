<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use App\Form\Login;
use App\Form\LoginValidator;
use App\Entity\User;
use App\Form\Contact;
use App\Form\ContactValidator;


class AdminController extends BaseController
{
    protected $_userTable;
    public    $user = null;

    public function indexAction()
    {
        $rdvMapper = $this->getServiceLocator()->get('rdvMapper');
        $rdv = $rdvMapper->fetchAll();

        return new ViewModel(array(
            'rdv' => $rdv,
        ));
    }

    public function onDispatch(MvcEvent $e)
    {
        $action = $this->getEvent()->getRouteMatch()->getParam('action');
        if ($action != 'login') {
            if ($this->userAuth()->hasIdentity()) {
                $this->user = $this->userAuth()->getIdentity();
            } else {
                return $this->redirect()->toRoute('App/admin');
            }
        }
        parent::onDispatch($e);
    }

    public function loginAction()
    {
        $error = false;
        $form = new Login();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formValidator = new LoginValidator();
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $messages = $this->_auth($data);
                $error = true;
                $form->get('email')->setMessages($messages);
            }
        }

        return new ViewModel(array(
            'error' => $error,
            'form'  => $form,
        ));
    }

    public function createAction()
    {
        $googleApi = $this->getServiceLocator()->get('calendar');
        // $googleApi->setScopes(\Google_Service_Calendar::CALENDAR);
        $event = new \Google_Service_Calendar_Event(array(
          'summary' => 'Google I/O 2015',
          'description' => 'A chance to hear more about Google\'s developer products.',
          'start' => array(
            'dateTime' => '2015-11-06T09:00:00-01:00',
            'timeZone' => 'Europe/Paris',
          ),
          'end' => array(
            'dateTime' => '2015-11-06T12:00:00-01:00',
            'timeZone' => 'Europe/Paris',
          ),
        ));

        $calendarId = 'primary';
        $event = $googleApi->events->insert($calendarId, $event);
        \Zend\Debug\Debug::dump('Event created: ' . $event->htmlLink);die;
        printf('Event created: %s\n', $event->htmlLink);
    }

    public function detailAction()
    {
        $id = $this->_params('id');
        $form = new Contact();
        $calendar = $this->getServiceLocator()->get('rdvMapper')->getById($id);

        return new ViewModel(array(
            'form'     => $form,
            'calendar' => $calendar,
        ));
    }



    protected function _auth($data)
    {
        $authAdapter = $this->getServiceLocator()->get('userDbAuth');
        $authAdapter->setParams(array('email' => $data['email'], 'password' => $data['password']));
        $result = $authAdapter->authenticate();
        if ($result->getCode() == 1) {
            $authStorage = $this->userAuth()->getAuthService()->getStorage();
            $authStorage->write($result->getIdentity());
            $this->redirect()->toRoute('home');
        }
        return $result->getMessages();
    }
}
