<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use App\Form\Login;
use App\Form\LoginValidator;
use App\Entity\Rdv;
use App\Form\Contact;
use App\Form\AdminContactValidator;
use App\Services\Date;
use App\Services\Mail;


class AdminController extends BaseController
{
    protected $_userTable;
    public    $user = null;

    public function indexAction()
    {
        $rdvMapper = $this->getServiceLocator()->get('rdvMapper');
        $rows = $rdvMapper->fetchAll([], 'date ASC');

        $results = array();
        foreach ($rows as $rdv) {
            $results[$rdv->status][$rdv->id] = $rdv->toArray();
            $results[$rdv->status][$rdv->id]['date'] = Date::translate($rdv->getDate()->format('l d F Y'));
        }

        return new ViewModel(array(
            'results' => $results,
        ));
    }

    public function onDispatch(MvcEvent $e)
    {
        $action = $this->getEvent()->getRouteMatch()->getParam('action');
        if ($action != 'login') {
            if ($this->userAuth()->hasIdentity()) {
                $this->user = $this->userAuth()->getIdentity();
            } else {
                return $this->redirect()->toRoute('App/admin', ['action' => 'login']);
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

    public function detailAction()
    {
        $id = $this->_params('id');
        $calendar = $this->getServiceLocator()->get('rdvMapper')->getById($id);
        $form = new Contact([
            'dates' => [],
            'times' => []
        ]);
        $data = $calendar->toArray();
        $data['full-date'] = $calendar->getDate()->format('d/m/Y H:i');
        $date = Date::translate($calendar->getDate()->format('l d M Y \à H\h'));
        $form->setData($data);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $formValidator = new AdminContactValidator();
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                try {
                    $googleApi = $this->getServiceLocator()->get('calendar');
                    $requestDate = \DateTime::createFromFormat('d/m/Y H:i', $data['full-date'], new \DateTimeZone('Europe/Paris'));
                    $event = new \Google_Service_Calendar_Event(array(
                      'summary' => $data['firstname'] . ' ' . $data['lastname'] . ' - ' . $data['phone'],
                      'description' => $data['comment'],
                      'start' => array(
                        'dateTime' => $requestDate->format(\Datetime::ATOM),
                        'timeZone' => 'Europe/Paris',
                      ),
                      'end' => array(
                        'dateTime' =>  $requestDate->modify('+ 1 hour')->format(\Datetime::ATOM),
                        'timeZone' => 'Europe/Paris',
                      ),
                    ));

                    $calendarId = 'osteo.defour@gmail.com';
                    $event = $googleApi->events->insert($calendarId, $event);

                    if (isset($data['email'])) {
                        // Emailing
                        $mail = new Mail($this->getServiceLocator()->get('mail'));
                        $mail->addFrom('osteo.defour@gmail.com');
                        $mail->addBcc($data['email']);
                        $mail->setSubject('[osteo-defour.fr] Rendez-vous du  - ' . \App\Services\Date::translate($requestDate->format('l d F Y \à H:i')) . ' confirmé');
                        $mail->setTemplate(Mail::TEMPLATE_CONFIRMATION, [
                            'firstname' => $data['firstname'],
                            'lastname'  => $data['lastname'],
                            'phone'     => $data['phone'],
                            'email'     => $data['email'],
                            'comment'   => $data['comment'],
                            'date'      => \App\Services\Date::translate($requestDate->format('l d F Y \à H:i')),
                            'baseUrl'   => '',
                        ]);
                        $mail->send();
                        $message = '<i class="fa fa-envelope"></i> Un email de confirmation a été envoyé au patient';
                        $this->getServiceLocator()->get('rdvMapper')->delete($id);
                    } else {
                        $message = '<i class="fa fa-exclamation-triangle"></i> Une confirmation téléphonique s\'impose';
                        $this->getServiceLocator()->get('rdvMapper')->fromArray([
                            'id'        => $calendar->id,
                            'firstname' => $data['firstname'],
                            'lastname'  => $data['lastname'],
                            'phone'     => $data['phone'],
                            'email'     => $data['email'],
                            'comment'   => $data['comment'],
                            'date'      => $requestDate->format('Y-m-d H:i:s'),
                            'status'    => Rdv::STATUS_NEED_CONFIRM,
                        ])->save();
                    }

                    $this->flashMessenger()->addMessage(
                        '<p>Rendez-vous ajouté au calendrier !</p> 
                         <p>' . $message . '</p>
                    ');
                } catch (Exception $e) {
                    $this->flashMessenger()->addErrorMessage(
                        '<p>Un problème est survenu lors de l\'ajout au calendrier</p>
                    ');
                }
                $this->redirect()->toRoute('App/admin');
            }
        }

        return new ViewModel(array(
            'date'     => $date,
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
