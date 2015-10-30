<?php
namespace App\Entity;

class User extends AbstractEntity
{
    protected $_id          = null;
    protected $_email       = null;
    protected $_password    = null;
    protected $_status      = null;

    public function toArray()
    {
        return array(
            'id'        => (int) $this->_id,
            'email'     => $this->_email,
            'password'  => $this->_password,
            'status'    => $this->_status,
        );
    }
}
