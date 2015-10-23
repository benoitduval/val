<?php

namespace App\Services;

/**
*
*/
class Date
{
    protected $_date;

    public static $translate = array(
        '/Monday/i'    => 'Lundi',
        '/Tuesday/i'   => 'Mardi',
        '/january/i'   => 'Janvier',
        '/february/i'  => 'Fevrier',
        '/march/i'     => 'Mars',
        '/april/i'     => 'Avril',
        '/may/i'       => 'Mai',
        '/june/i'      => 'Juin',
        '/july/i'      => 'Juillet',
        '/august/i'    => 'Aout',
        '/septembre/i' => 'Septembre',
        '/october/i'   => 'Octobre',
        '/november/i'  => 'Novembre',
        '/december/i'  => 'Decembre',
    );

    public function __construct($date)
    {
        $this->_date = \DateTime::createFromFormat('U', $date);
    }

    public static function translate($date)
    {
        return preg_replace(array_keys(static::$translate), array_values(static::$translate), $date);
    }

    public function modify($string)
    {
        return $this->_date->modify($string);
    }

}
