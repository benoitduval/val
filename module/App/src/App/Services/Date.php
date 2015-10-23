<?php

namespace App\Services;

/**
*
*/
class Date
{
    protected $_date;

    public static $translate = array(
        '/Mon/i'  => 'Lun',
        '/Tue/i'  => 'Mar',
        '/Wed/i'  => 'Mer',
        '/Thu/i'  => 'Jeu',
        '/Fri/i'  => 'Ven',
        '/Sat/i'  => 'Sam',
        '/Sun/i'  => 'Dim',
        '/jan/i'  => 'Jan',
        '/feb/i'  => 'Fev',
        '/mar/i'  => 'Mar',
        '/apr/i'  => 'Avr',
        '/may/i'  => 'Mai',
        '/jun/i'  => 'Juin',
        '/jul/i'  => 'Juil',
        '/aug/i'  => 'Aout',
        '/sep/i'  => 'Sept',
        '/oct/i'  => 'Oct',
        '/nov/i'  => 'Nov',
        '/dec/i'  => 'Dec',
    );

    public function __construct($date)
    {
        $this->_date = \DateTime::createFromFormat('U', $date);
    }

    public function format($format = 'D d M \- H\hi')
    {
        $date = $this->_date->format($format);
        return preg_replace(array_keys(static::$translate), array_values(static::$translate), $date);
    }
}
