<?php
namespace App\Auth\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class Db implements AdapterInterface
{

    protected $_email;
    protected $_userMapper;
    protected $_password;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($email = null, $password = null)
    {
        $this->_email    = $email;
        $this->_password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        $user = $this->getUserMapper()->getByEmail($this->_email);
        if (!$user) return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, array('Identity not found.'));
        if (md5($this->_password) != $user->password) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, array('Invalid Password.'));
        }

        return new Result(Result::SUCCESS, $user, array('Authentication successful.'));
    }

    public function getUserMapper()
    {
        return $this->_userMapper;
    }

    public function setUserMapper($userMapper)
    {
        $this->_userMapper = $userMapper;
        return $this;
    }

    public function setParams(array $params)
    {
        $this->_email    = $params['email'];
        $this->_password = $params['password'];
    }
}