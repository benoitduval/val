<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use App\Form\Contact;
use App\Form\ContactValidator;
use App\Services\Mail;
use App\Services\GoogleApi;
use App\Entity\Rdv;

class IndexController extends BaseController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function detailAction()
    {
        $calendar = $this->getServiceLocator()->get('calendar');
        
        // build Dates
        $dates = [0 => 'Date'];
        $date = new \DateTime('now');
        for ($i = 1; $i < 10; $i++) {
            $date = $date->modify('next monday');
            $dates[$date->format('Ymd')] = \App\Services\Date::translate($date->format('l d F Y'));
            $date = $date->modify('next tuesday');
            $dates[$date->format('Ymd')] = \App\Services\Date::translate($date->format('l d F Y'));
        }

        // build time
        $formData = [
            'dates' => $dates,
            'times' => [
                0  => 'Heure',
                9  => '9h00',
                10 => '10h00',
                11 => '11h00',
                12 => '12h00',
                13 => '13h00',
                14 => '14h00',
                15 => '15h00',
                16 => '16h00',
                17 => '17h00',
                18 => '18h00',
                19 => '19h00',
                20 => '20h00',
            ]
        ];

        $form = new Contact($formData);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formValidator = new ContactValidator($request->getPost()->toArray());
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $date = \Datetime::createFromFormat('Ymd H', $data['date'] . ' ' . $data['time']);

                // save RDV in DB
                $rdvMapper = $this->getServiceLocator()->get('rdvMapper');
                $rdv = $rdvMapper->fromArray([
                    'email'     => $data['email'],
                    'firstname' => $data['firstname'],
                    'lastname'  => $data['lastname'],
                    'status'    => 0,
                    'date'      => $date->format('Y-m-d H:i:s'),
                    'phone'     => $data['phone'],
                    'comment'   => $data['comment'],
                ])->save();

                // Emailing
                $mail = new Mail($this->getServiceLocator()->get('mail'));
                $mail->addFrom('osteo.defour@gmail.com');
                $mail->addBcc('benoit.duval.pro@gmail.com');
                $mail->setSubject('[osteo-defour.fr] Demande de RDV - ' . $data['firstname'] . ' ' . $data['lastname']);
                $mail->setTemplate(Mail::TEMPLATE_RDV, [
                    'id'        => $rdv->id,
                    'firstname' => $data['firstname'],
                    'lastname'  => $data['lastname'],
                    'phone'     => $data['phone'],
                    'email'     => $data['email'],
                    'comment'   => $data['comment'],
                    'date'      => \App\Services\Date::translate($date->format('l d F Y \à H:i')),
                    'baseUrl'   => '',
                ]);
                $mail->send();

                $this->flashMessenger()->addMessage(
                    '<p>Demande de rendez-vous prise en compte.</p> 
                     <p>Nous vous confirmerons ce rendez-vous dans les plus brefs délais.</p>
                ');
                $this->redirect()->toRoute('App/profile');
            } else {
                $inputErrors = array_keys($form->getMessages());
                foreach ($inputErrors as $input) {
                    $form->get($input)->setAttribute('class', 'form-control has-error');
                }
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }

    public function calendarAction()
    {
        $calendar = $this->getServiceLocator()->get('calendar');
        $date     = $this->params('date');
        $startDay = \Datetime::createFromFormat('Ymd H:i:s', $date . ' 08:00:00');
        $endDay   = \Datetime::createFromFormat('Ymd H:i:s', $date . ' 21:00:00');

        $optParams = [
            'orderBy'      => 'startTime',
            'singleEvents' => TRUE,
            'timeMin'      => $startDay->format(\Datetime::ATOM),
            'timeMax'      => $endDay->format(\Datetime::ATOM),
        ];

        $config = $this->getServiceLocator()->get('config');
        $results = $calendar->events->listEvents($config['api']['googleapi']['calendarId'], $optParams);

        if ($startDay->format('l') == 'Monday') {
            $dates = [0 => 'Heure', 10 => '10h00', 11 => '11h00', 12 => '12h00', 13 => '13h00', 15 => '15h00', 16 => '16h00', 17 => '17h00', 18 => '18h00', 19 => '19h00', 20 => '20h00'];
        } else {
            $dates = [0 => 'Heure', 9 => '09h00',10 => '10h00', 11 => '11h00', 12 => '12h00', 13 => '13h00', 15 => '15h00', 16 => '16h00'];
        }
        if (count($results->getItems())) {
            foreach ($results->getItems() as $event) {
                if ($start = $event->start->dateTime) {
                    $end       = $event->end->dateTime;
                    $startDate = \Datetime::createFromFormat(\Datetime::ATOM, $start);
                    if (!in_array($startDate->format('l'), ['Monday', 'Tuesday'])) continue;
                    $endDate   = \Datetime::createFromFormat(\Datetime::ATOM, $end);
                    for($i = $startDate->format('H'); $i < $endDate->format('H'); $i++) {
                        unset($dates[$i]);
                    }
                } else if ($start = $event->start->date) {
                    $dates = [0 => 'Aucune Place'];
                }
            }
        }

        if (count($dates) == 1) $dates = [0 => 'Aucune Place'];

        $view = new ViewModel(array(
            'result' => $dates
        ));

        $view->setTerminal(true);
        $view->setTemplate('app/index/json.phtml');
        return $view;
    }
}
