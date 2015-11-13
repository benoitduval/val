<?php
namespace App\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class Contact extends Form
{
    public function __construct($params = array(), $name = null)
    {
        parent::__construct('contact');

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
            'name' => 'full-date',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Date',
                'id'    => 'full-date',
            ),
            'options' => array(
                'label' => 'Date',
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
            'name' => 'weekDay',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'options' => array('Jour', 'Lundi', 'Mardi')
            ),
            'attributes' => array(
                'id'    => 'weekDay',
                'class' => 'form-control',
                'required' => 'required',
            )
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'options' => $params['dates']
            ),
            'attributes' => array(
                'id'    => 'date',
                'class' => 'form-control',
                'required' => 'required',
                'disabled' => 'disabled'
            )
        ));

        $this->add(array(
            'name' => 'time',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'options' => $params['times']
            ),
            'attributes' => array(
                'id'    => 'time',
                'class' => 'form-control',
                'required' => 'required',
                'disabled' => 'disabled'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'id'    => 'eventSubmit',
                'class' => 'btn btn-primary btn-lg',
                'type' => 'submit',
                'value' => 'Envoyer',
            )
        ));
    }
}
