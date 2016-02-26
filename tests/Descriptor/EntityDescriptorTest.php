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
use Slick\Orm\Descriptor\Field\FieldsCollection;

/**
 * Entity Descriptor test case
 *
 * @package Slick\Tests\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityDescriptorTest extends TestCase
{

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
}
