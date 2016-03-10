<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Descriptor;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Descriptor\EntityDescriptor;
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\Descriptor\Field\FieldsCollection;
use Slick\Orm\Mapper\Relation\AbstractRelation;
use Slick\Orm\Mapper\RelationInterface;

/**
 * Entity Descriptor test case
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityDescriptorTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        EntityDescriptor::addRelation('MyRelation', TestRelation::class);
    }

    public function names()
    {
        return [
            'single' => ['Person', 'people'],
            'path' => ['Path\To\Class', 'classes'],
            'composed' => ['Path\Name\UserType', 'userTypes']
        ];
    }

    /**
     * @param string $name
     * @param string $expected
     *
     * @dataProvider names
     */
    public function testTableName($name, $expected)
    {
        $this->assertEquals(
            $expected,
            EntityDescriptor::parseTableName($name)
        );
    }

    /**
     * Should read the annotation to determine the table name
     * @test
     */
    public function getTableByAnnotation()
    {
        /** @var Person $entity */
        $entity = Person::class;
        $descriptor = new EntityDescriptor($entity);
        $this->assertEquals('users', $descriptor->getTableName());
    }

    /**
     * Should return the table fields collection
     * @test
     */
    public function getFields()
    {
        $entity = Person::class;
        $descriptor = new EntityDescriptor($entity);
        $fields = $descriptor->getFields();
        $this->assertInstanceOf(FieldsCollection::class, $fields);
        return $fields;
    }

    /**
     * Should contain id and name fields
     * @param FieldsCollection $fields
     * @test
     * @depends getFields
     */
    public function checkFields(FieldsCollection $fields)
    {
        $expected = ['uid', 'name'];
        $data = array_keys($fields->asArray());
        $this->assertEquals($expected, $data);
    }

    public function testPrimaryKey()
    {
        $entity = Person::class;
        $descriptor = new EntityDescriptor($entity);
        $primaryKey = $descriptor->getPrimaryKey();
        $this->assertInstanceOf(FieldDescriptor::class, $primaryKey);
        return $primaryKey;
    }

    /**
     * @param FieldDescriptor $primaryKey
     * @depends  testPrimaryKey
     */
    public function testCorrectField($primaryKey)
    {
        $this->assertEquals('uid', $primaryKey->getField());
        $this->assertEquals('id', $primaryKey->getName());
    }

    public function testClassName()
    {
        $entity = Person::class;
        $descriptor = new EntityDescriptor($entity);
        $this->assertEquals($entity, $descriptor->className());
    }

    /**
     * @test
     */
    public function checkRelation()
    {
        $entity = Person::class;
        $descriptor = new EntityDescriptor($entity);
        $rel = $descriptor->getRelationsMap()->get('testRelation');
        $this->assertInstanceOf(TestRelation::class, $rel);
    }

    /**
     * Should throw an exception
     * @test
     * @expectedException \Slick\Orm\Exception\InvalidArgumentException
     */
    public function addInvalidRelation()
    {
        EntityDescriptor::addRelation('test', 'stdClass');
    }
}


class TestRelation extends AbstractRelation implements RelationInterface
{

    /**
     * @write
     * @var string
     */
    protected $annotation;
}