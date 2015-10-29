<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use App\Form\Contact;
use App\Form\ContactValidator;
use App\Services\Mail;
use Zend\Debug\Debug;
use App\Services\GoogleApi;

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
        $dates = [0 =>'Date'];
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
                $mail = new Mail($this->getServiceLocator()->get('mail'));
                $email = $data['email'] ? $data['email'] : 'no-reply@osteo-defour.fr';
                $mail->addFrom($email);
                $mail->addBcc('benoit.duval.pro@gmail.com');
                $mail->setSubject('[osteo-defour.fr] Demande de RDV - ' . $data['firstname'] . ' ' . $data['lastname']);
                $mail->setTemplate(Mail::TEMPLATE_RDV, [
                    'firstname' => $data['firstname'],
                    'lastname'  => $data['lastname'],
                    'phone'     => $data['phone'],
                    'email'     => $data['email'],
                    'comment'   => $data['comment'],
                    'date'      => \App\Services\Date::translate($date->format('l d F Y \à H:i')),
                    'baseUrl'   => '',
                ]);
                $mail->send();
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
}
