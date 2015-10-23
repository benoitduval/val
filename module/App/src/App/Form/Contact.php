<?php
namespace App\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Contact extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('volley');

        $this->setAttributes(array(
            'method'=> 'post',
            'id'    => 'contact-form'
        ));

        $this->add(array(
            'name' => 'firstname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Prénom *',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Prénom *',
                'label_attributes' => array(
                    'class'  => 'control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'lastname',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Nom *',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Nom',
                'label_attributes' => array(
                    'class'  => 'control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Email',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Email',
            ),
            'options' => array(
                'label' => 'Email',
                'label_attributes' => array(
                    'class'  => 'control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Téléphone *',
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Prénom',
                'label_attributes' => array(
                    'class'  => 'control-label'
                ),
            ),
        ));

        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'placeholder' => 'Votre message *',
                'class' => 'form-control',
                'required' => 'required',
                'rows' => 4,
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-lg',
                'type' => 'submit',
                'value' => 'Envoyer',
            )
        ));
    }
}
