<?php
namespace App\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Login extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('login');

        $this->setAttributes(array(
            'method' => 'post',
            'class'  => 'form-horizontal'
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'class' => 'form-control first',
                'placeholder' => 'Email',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Email',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'attributes' => array(
                'class' => 'form-control last',
                'placeholder' => 'Password',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Mot de passe',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-lg',
                'type' => 'submit',
                'value' => 'S\'identifier',
            )
        ));
    }
}
