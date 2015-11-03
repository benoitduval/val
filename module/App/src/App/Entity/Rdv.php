<?php
namespace App\Entity;

class Rdv extends AbstractEntity
{
    const STATUS_CREATE       = 0;
    const STATUS_CONFIRMED    = 1;
    const STATUS_NEED_CONFIRM = 2;

    protected $_id         = null;
    protected $_email      = null;
    protected $_phone      = null;
    protected $_firstname  = null;
    protected $_lastname   = null;
    protected $_comment    = null;
    protected $_date       = null;
    protected $_status     = null;

    public function toArray()
    {
        return array(
            'id'        => (int) $this->_id,
            'email'     => $this->_email,
            'phone'     => $this->_phone,
            'firstname' => $this->_firstname,
            'lastname'  => $this->_lastname,
            'comment'   => $this->_comment,
            'date'      => $this->_date,
            'status'    => $this->_status,
        );
    }

    public function getDate() {
        return \Datetime::createFromFormat('Y-m-d H:i:s', $this->_date, new \DateTimeZone('Europe/Paris'));
    }
}
