<?php
namespace App\Entity;

class Rdv extends AbstractEntity
{
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
            'phone'     => $this->phone,
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'comment'   => $this->comment,
            'date'      => $this->date,
            'status'    => $this->_status,
        );
    }
}
