<?php
namespace NestedSetModel;

use NestedSetModel\Model\Category;
use NestedSetModel\Model\CategoryTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(            
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__.'/autoload_classmap.php'
            ),            
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
          'factories' => array(
              'NestedSetModel\Model\CategoryTable' => function($sm){
                $tableGateway = $sm->get('CategoryTableGateway');
                $table = new CategoryTable($tableGateway);
                return $table;
              },
              'CategoryTableGateway' => function($sm){
                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                  $resultSetPrototype = new ResultSet();
                  $resultSetPrototype->setArrayObjectPrototype(new Category());
                  return new TableGateway('category', $dbAdapter, null, $resultSetPrototype);
              }
          )  
        );
    }    
        
}
