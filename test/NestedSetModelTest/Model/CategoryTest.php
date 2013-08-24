<?php
namespace NestedSetModelTest\Model;

use NestedSetModel\Model\Category;
use PHPUnit_Framework_TestCase;

class CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testCategoryInitialState()
    {
        $category = new Category();

        $this->assertNull(
            $category->name,
            '"name" should initially be null'
        );
        $this->assertNull(
            $category->id,
            '"id" should initially be null'
        );
        $this->assertEquals(
            0,
            $category->lft,
            '"lft" should initially be 0'
        );
        $this->assertEquals(
            1,
            $category->rgt,
            '"rgt" should initially be 1'
        );        
    }

    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $category = new Category();
        $data  = array('name' => 'some category',
                       'id'     => 123,
                       'lft'  => 10,
                        'rgt' => 11);

        $category->exchangeArray($data);

        $this->assertSame(
            $data['name'],
            $category->name,
            '"name" was not set correctly'
        );
        $this->assertSame(
            $data['id'],
            $category->id,
            '"id" was not set correctly'
        );
        $this->assertSame(
            $data['lft'],
            $category->lft,
            '"lft" was not set correctly'
        );
        $this->assertSame(
            $data['rgt'],
            $category->rgt,
            '"rgt" was not set correctly'
        );
    }

    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent()
    {
        $category = new Category();

        $category->exchangeArray(array());

        $this->assertNull(
            $category->name, '"name" should have defaulted to null'
        );
        $this->assertEquals(
            0,
            $category->lft, '"lft" should have defaulted to null'
        );
        $this->assertEquals(
            1,
            $category->rgt, '"rgt" should have defaulted to null'
        );
    }

    public function testGetArrayCopyReturnsAnArrayWithPropertyValues()
    {
        $category = new Category();
        $data  = array('name' => 'some category',
                       'id'     => 123,
                       'lft'  => 10,
                        'rgt' => 11);

        $category->exchangeArray($data);
        $copyArray = $category->getArrayCopy();

        $this->assertSame(
            $data['name'],
            $copyArray['name'],
            '"name" was not set correctly'
        );
        $this->assertSame(
            $data['id'],
            $copyArray['id'],
            '"id" was not set correctly'
        );
        $this->assertSame(
            $data['lft'],
            $copyArray['lft'],
            '"lft" was not set correctly'
        );
        $this->assertSame(
            $data['rgt'],
            $copyArray['rgt'],
            '"rgt" was not set correctly'
        );
    }

    public function testInputFiltersAreSetCorrectly()
    {
        $category = new Category();

        $inputFilter = $category->getInputFilter();

        $this->assertSame(4, $inputFilter->count());
        $this->assertTrue($inputFilter->has('name'));
        $this->assertTrue($inputFilter->has('id'));

    }
}