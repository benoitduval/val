<?php

namespace App;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use App\Services\GoogleApi;


class Module
{
    protected static $_sm;

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'userAuth' => function ($sm) {
                    $serviceLocator = $sm->getServiceLocator();
                    $authService = $serviceLocator->get('authService');
                    $authAdapter = $serviceLocator->get('App\Auth\Adapter\Db');
                    $controllerPlugin = new Controller\Plugin\UserAuth;
                    $controllerPlugin->setAuthService($authService);
                    $controllerPlugin->setAuthAdapter($authAdapter);

                    return $controllerPlugin;
                },
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => [
                'App\Auth\Adapter\Db'  => 'App\Auth\Adapter\Db',
            ],
            'factories' => [
                'authService' => function ($sm) {
                    return new \Zend\Authentication\AuthenticationService(
                        $sm->get('App\Auth\Storage\Cookie'),
                        $sm->get('App\Auth\Adapter\Db')
                    );
                },
                'App\Auth\Storage\Cookie' => function ($sm) {
                    $storage = new \App\Auth\Storage\Cookie();
                    $storage->setUserMapper($sm->get('userMapper'));

                    return $storage;
                },

                'userDbAuth' => function ($sm) {
                    $adapter = new \App\Auth\Adapter\Db();
                    $adapter->setUserMapper($sm->get('userMapper'));

                    return $adapter;
                },

                // Service Mail
                'mail' => function ($sm) {
                    $config   = $sm->get('config');
                    $address  = $config['mail']['address'];
                    $password = $config['mail']['password'];

                    // Setup SMTP transport using LOGIN authentication
                    $transport = new SmtpTransport();
                    $options   = new SmtpOptions([
                        'host'              => 'smtp.gmail.com',
                        'connection_class'  => 'login',
                        'connection_config' => [
                            'ssl'       => 'tls',
                            'username' => $address,
                            'password' => $password
                        ],
                        'port' => 587,
                    ]);

                    $transport->setOptions($options);
                    return $transport;
                },

                // Service google API
                'googleApi' => function ($sm) {
                    $config    = $sm->get('config');
                    return new GoogleApi($config['api']['googleapi']);
                },

                // Service google API
                'calendar' => function ($sm) {
                    $api    = $sm->get('googleApi');
                    $result = $api->getApiClient();
                    if (isset($result['url'])) header('Location: '. $result['url']);
                    return new \Google_Service_Calendar($result['client']);
                },

                /******************************
                 * MAPPERS
                 ******************************/

                'userMapper' => function ($sm) {
                    if (is_null(static::$_sm)) static::$_sm = $sm;
                    $mapper = static::getMapper('user');

                    return $mapper;
                },

                'rdvMapper' => function ($sm) {
                    if (is_null(static::$_sm)) static::$_sm = $sm;
                    $mapper = static::getMapper('rdv');

                    return $mapper;
                },

                /******************************
                 * GATEWAY
                 ******************************/
                'UserTableGateway' => function ($sm) {
                    return static::getTableGateway('user');
                },
                'RdvTableGateway' => function ($sm) {
                    return static::getTableGateway('rdv');
                },
            ]
        );
    }

    protected static function getMapper($name)
    {
        $mapperClass = '\App\Mapper\\' . ucfirst($name);
        $entityClass = '\App\Entity\\' . ucfirst($name);
        $gatewayName = ucfirst($name) . 'TableGateway';
        $mapper = new $mapperClass();
        $tableGateway = static::$_sm->get($gatewayName);
        $mapper->setEntity(new $entityClass);
        $mapper->setTableGateway($tableGateway);

        return $mapper;
    }

    protected static function getTableGateway($name)
    {
        $table = static::getDbPrefix($name);
        $dbAdapter = static::$_sm->get('Zend\Db\Adapter\Adapter');
        $resultSetPrototype = new ResultSet();
        $entityClass = '\App\Entity\\' . ucfirst($name);
        $resultSetPrototype->setArrayObjectPrototype(new $entityClass());

        return new TableGateway($table, $dbAdapter, null, $resultSetPrototype);
    }

    protected static function getDbPrefix($name)
    {
        $config = static::$_sm->get('config');

        return isset($config['db']['prefix']) ? $config['db']['prefix'] . $name: $name;
    }
}
