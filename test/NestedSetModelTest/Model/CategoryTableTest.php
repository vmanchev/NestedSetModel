<?php
namespace NestedSetModelTest\Model;

use NestedSetModel\Model\CategoryTable;
use NestedSetModel\Model\Category;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class CategoryTableTest extends PHPUnit_Framework_TestCase
{
    public function testFetchAllReturnsAllCategories()
    {
        $resultSet = new ResultSet();
        $mockTableGateway = $this->getMock(
            'Zend\Db\TableGateway\TableGateway',
            array('select'),
            array(),
            '',
            false
        );
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSet));

        $categoryTable = new CategoryTable($mockTableGateway);

        $this->assertSame($resultSet, $categoryTable->fetchAll());
    }
}