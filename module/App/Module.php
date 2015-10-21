<?php

namespace App;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

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
        );
    }

    public function getServiceConfig()
    {
        return array(
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
