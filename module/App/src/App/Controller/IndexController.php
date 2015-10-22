<?php
namespace App\Controller;

use Zend\View\Model\ViewModel;
use App\Form\CreatePlace;
use App\Form\CreatePlaceValidator;
use App\Entity\Place;

class IndexController extends BaseController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function detailAction()
    {
       return new ViewModel();
    }
}
