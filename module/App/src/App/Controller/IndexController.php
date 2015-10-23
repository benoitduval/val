<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use App\Form\Contact;
use App\Form\ContactValidator;

class IndexController extends BaseController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function detailAction()
    {
        $form = new Contact();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $formValidator = new ContactValidator($request->getPost()->toArray());
            $form->setInputFilter($formValidator->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                
            } else {
                $inputErrors = array_keys($form->getMessages());
            }
        }
        return new ViewModel([
            'form' => $form
        ]);
    }
}
