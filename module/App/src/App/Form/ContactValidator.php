<?php

namespace App\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ContactValidator implements InputFilterAwareInterface
{
    protected $_inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(
                array(
                    'name' => 'email',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => [
                        [
                            'name' => 'EmailAddress',
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min'      => 5,
                                'max'      => 255,
                                'messages' => array(
                                    \Zend\Validator\EmailAddress::INVALID_FORMAT => 'Emzail address format is invalid'
                                )
                            ],
                        ],
                    ],
                )
            ));

            $inputFilter->add($factory->createInput(array(
                'name' => 'phone',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ), 
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^0[0-9]{9}$/',
                            'messages' => array(
                                \Zend\Validator\Regex::INVALID => 'Numéro de téléphone invalide',
                            ),
                        ),
                    ),
                ), 
            )));


            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }
}
