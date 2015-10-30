<?php

namespace App\Mapper;

class User extends AbstractMapper
{

    protected $_groupMapper = null;

    public function getByEmail($email)
    {
        $resultRow  = $this->getTableGateway()->select(array('email' => $email));
        $user = $resultRow->current();
        return $user;
    }
}