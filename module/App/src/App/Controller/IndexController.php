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
        // build Dates
        $dates = [0 =>'Date'];
        $date = new \DateTime('now');
        for ($i = 1; $i < 10; $i++) {
            $date = $date->modify('next monday');
            $dates[$date->format('U')] = \App\Services\Date::translate($date->format('l d F Y'));
            $date = $date->modify('next tuesday');
            $dates[$date->format('U')] = \App\Services\Date::translate($date->format('l d F Y'));
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
                $date = new \App\Services\Date($data['date']);
                $mail = new Mail($this->getServiceLocator()->get('mail'));
                $email = $data['email'] ? $data['email'] : 'no-reply@osteo-defour.fr';
                $mail->addFrom($email);
                $mail->addBcc('benoit.duval.pro@gmail.com');
                $mail->setSubject('[osteo-defour.fr] Demande de RDV');
                $mail->setTemplate(Mail::TEMPLATE_RDV, array(
                    'firstname' => $data['firstname'],
                    'lastname'  => $data['lastname'],
                    'phone'     => $data['phone'],
                    'email'     => $data['email'],
                    'comment'   => $data['comment'],
                    'date'      => $date->format('D d M Y'),
                    'time'      => $data['time'],
                    'baseUrl'   => '',
                ));
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

    public function googleAction()
    {
        // Get the API client and construct the service object.
        $code = $this->params()->fromQuery('code', false);
        $googleApi = $this->getServiceLocator()->get('googleApi');
        if ($code) {
            $result = $googleApi->authenticate($code);
        } else {
            $result = $googleApi->getApiClient();
            if (isset($result['url'])) {
                \Zend\Debug\Debug::dump($result['url']);die;
            } else {
                $client = $result['client'];
            }

            $service = new \Google_Service_Calendar($client);

            // Print the next 10 events on the user's calendar.
            $calendarId = 'primary';
            $optParams = array(
                'maxResults' => 10,
                'orderBy' => 'startTime',
                'singleEvents' => TRUE,
                'timeMin' => date('c'),
            );
            $results = $service->events->listEvents($calendarId, $optParams);

            if (count($results->getItems()) == 0) {
                print "No upcoming events found.\n";
            } else {
                print "Upcoming events:\n";
                foreach ($results->getItems() as $event) {
                    \Zend\Debug\Debug::dump($event);die;
                    $start = $event->start->dateTime;
                    if (empty($start)) {
                        $start = $event->start->date;
                    }
                    printf("%s (%s)\n", $event->getSummary(), $start);
                    \Zend\Debug\Debug::dump(array($event->getSummary(), $start));die;
                }
            }
        }
    }
}
