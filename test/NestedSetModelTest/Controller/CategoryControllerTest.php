<?php

namespace NestedSetModelTest\Controller;

use NestedSetModel\Model\Category;
use NestedSetModel\Model\CategoryTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CategoryControllerTest extends AbstractHttpControllerTestCase {

    protected $traceError = true;

    public function setUp() {
        $this->setApplicationConfig(
                include '/home/venelin/websites/zf2/config/application.config.php'
        );
        parent::setUp();
    }


    public function testCategoryIndexIsAccessible(){
        $categoryTableMock = $this->getMockBuilder('NestedSetModel\Model\CategoryTable')
                ->disableOriginalConstructor()
                ->getMock();
        
        $categoryTableMock->expects($this->once())
                ->method('fetchAll')
                ->will($this->returnValue(array()));        
        
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('NestedSetModel\Model\CategoryTable', $categoryTableMock);        
        
        $this->dispatch('/category');
        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('NestedSetModel');
        $this->assertControllerName('NestedSetModel\Controller\Category');
        $this->assertControllerClass('CategoryController');
        $this->assertMatchedRouteName('nested_category');
    }

    public function testAddNewCategoryIsAccessible(){
        
        $this->dispatch('/category/add/1');
        
        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('NestedSetModel');
        $this->assertControllerName('NestedSetModel\Controller\Category');
        $this->assertControllerClass('CategoryController');
        $this->assertActionName('add');
        $this->assertMatchedRouteName('nested_category');
        
    }
    
    public function testAddNewCategoryIsSuccessfull(){
        
        $categoryTableMock = $this->getMockBuilder('NestedSetModel\Model\CategoryTable')
                ->disableOriginalConstructor()
                ->getMock();

        $categoryTableMock->expects($this->once())
                ->method('saveCategory')
                ->will($this->returnValue(null));

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('NestedSetModel\Model\CategoryTable', $categoryTableMock);        
        
        $this->dispatch(
                '/category/add/1',
                'POST',
                array(
                    'id'   => 0,
                    'name' => 'test category'
                )
        );
        
        $this->assertResponseStatusCode(302);

        $this->assertRedirectRegex('/\/category[\/?]/');
    }
    
    public function testEditActionRedirectsAfterValidPost(){
        
        $this->assertResponseStatusCode(200);
        
        $this->dispatch('/category/edit/1', 'POST', array(
            'id'   => 1,
            'name' => 'test category updated'
        ));
        
        $this->assertResponseStatusCode(302);

        $this->assertRedirectRegex('/\/category[\/?]/');        
    }
    
    public function testCanRetreiveCategoryById()
    {
        $category = new Category();
        $category->exchangeArray(array(
            'id' => 1,
            'name' => 'test category'
        ));
        
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new Category());
        $resultSet->initialize(array($category));
        
        $mockTableGateway = $this->getMock(
                'Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false
        );
        
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with(array('id' => 1))
                         ->will($this->returnValue($resultSet));
        
        $categoryTable = new CategoryTable($mockTableGateway);
        
        $this->assertSame($category, $categoryTable->getCategory(1));
    }
    
    public function testCanDeleteCategoryById(){
        
        $mockTableGateway = $this->getMock(
                'Zend\Db\TableGateway\TableGateway', array('delete'), array(), '', false
        );
        
        $mockTableGateway->expects($this->once())
                         ->method('delete')
                         ->with(array('id' => 1));
        
        $categoryTable = new CategoryTable($mockTableGateway);
        $categoryTable->deleteCategory(1);
    }
    
    public function testThrowExceptionOnNonExistingCategory(){
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new Category());
        $resultSet->initialize(array());

        $mockTableGateway = $this->getMock(
                'Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false
        );
        $mockTableGateway->expects($this->once())
                ->method('select')
                ->with(array('id' => 1))
                ->will($this->returnValue($resultSet));

        $categoryTable = new CategoryTable($mockTableGateway);

        try {
            $categoryTable->getCategory(1);
        } catch (\Exception $e) {
            $this->assertSame('Could not find row 1', $e->getMessage());
            return;
        }

        $this->fail('Expected exception was not thrown');        
    }
}

