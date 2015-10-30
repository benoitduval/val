<?php
namespace App\Auth\Storage;

use Zend\Authentication\Storage\StorageInterface;
use Zend\Crypt\Password\Bcrypt;

class Cookie implements StorageInterface
{
    protected $_mapper;
    /**
     * Returns true if and only if storage is empty
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     *               If it is impossible to
     *               determine whether storage is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return ($this->read() === null);
    }

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     *               If reading contents from storage is impossible
     * @return mixed
     */

    public function read()
    {
        if (empty($_COOKIE['userId']) || empty($_COOKIE['cs'])) return null;
        $user = $this->getUserMapper()->getById($_COOKIE['userId']);
        if (!$user) return null;

        $bcrypt = new Bcrypt;
        if ($bcrypt->verify($user->email . $user->password, $_COOKIE['cs'])) {
            return $user;
        }
        $this->clear();
        return null;
    }

    /**
     * Writes $user to storage
     *
     * @param  mixed $contents
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     *               If writing $contents to storage is impossible
     * @return void
     */

    public function write($user)
    {
        // validate our parameter
        if (!$user instanceof \App\Entity\User) {
            throw new \Exception('no valid user provided in ' . __METHOD__);
        }

        $bcrypt = new Bcrypt;
        $salt = $bcrypt->create($user->email . $user->password);
        // get user data to store in cookies
        // set authentication cookies (both to browser and current PHP process)
        setcookie('cs', $salt, strtotime('+1 year'), '/');
        $_COOKIE['cs'] = $salt;
        setcookie('userId', $user->id, strtotime('+1 year'), '/');
        $_COOKIE['userId'] = $user->id;
    }

    /**
     * Clears contents from storage
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     *               If clearing contents from storage is impossible
     * @return void
     */

    public function clear()
    {
        $cookies = array_keys($_COOKIE);

        // remove all the given cookies
        $pastTime = time() - 42000;
        foreach ((array)$cookies as $cookie) {
            setcookie($cookie, '', $pastTime, '/');
            if (isset($_COOKIE[$cookie])) {
                unset($_COOKIE[$cookie]);
            }
        }
    }

    public function getUserMapper()
    {
        return $this->_mapper;
    }

    public function setUserMapper($userMapper)
    {
        $this->_mapper = $userMapper;
        return $this;
    }
}