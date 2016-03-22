<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Mapper\Relation;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Database\RecordList;
use Slick\Database\Sql\Dialect;
use Slick\Database\Sql\Select;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\Event\Save;
use Slick\Orm\Mapper\Relation\BelongsTo;
use Slick\Tests\Orm\Descriptor\Person;
use Slick\Tests\Orm\Descriptor\Profile;


/**
 * BelongsTo test case
 *
 * @package Slick\Tests\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BelongsToTest extends TestCase
{

    /**
     * @var BelongsTo
     */
    protected $belongsTo;

    /**
     * @var \Slick\Orm\Annotations\BelongsTo
     */
    protected $annotation;

    /**
     * Sets the SUT relation object.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->belongsTo = new BelongsTo(
            [
                'annotation' => $this->getAnnotation(),
                'propertyName' => 'person',
                'entityDescriptor' => EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor(Profile::class)
            ]
        );
        EntityDescriptorRegistry::getInstance()->getDescriptorFor(Profile::class)
            ->getRelationsMap()->set('person', $this->belongsTo);
    }

    /**
     * Cleaning for next test
     */
    protected function tearDown()
    {
        $this->belongsTo = null;
        parent::tearDown();
    }

    /**
     * Should return the property name
     * @test
     */
    public function getProperty()
    {
        $this->assertEquals('person', $this->belongsTo->getPropertyName());
    }

    /**
     * Should do nothing and return
     * @test
     */
    public function beforeSelectLazy()
    {
        $query = $this->getQueryMock();
        $query->expects($this->never())
            ->method('join');
        $this->belongsTo->lazyLoaded = true;
        $event = new \Slick\Orm\Event\Select(null, ['query' => $query]);
        $this->belongsTo->beforeSelect($event);
    }

    /**
     * Should load entity from database
     * @test
     */
    public function loadEntity()
    {
        $profile = New Profile(['id' => 2, 'email' => 'John@example.com']);
        $adapter = $this->getMockedAdapter();
        $adapter->expects($this->once())
            ->method('query')
            ->with($this->isInstanceOf(Select::class), [':id' => 2])
            ->willReturn(new RecordList());
        $this->belongsTo->setAdapter($adapter);
        $this->belongsTo->load($profile);
    }

    /**
     * Should add a join to the passed select event
     * @test
     */
    public function beforeSelect()
    {
        $query = $this->getQueryMock();
        $query->expects($this->once())
            ->method('join')
            ->with(
                'users',
                'profiles.user_id = users.uid',
                [
                    'uid AS users_uid',
                    'name AS users_name'
                ],
                'users'
            )
            ->willReturn($this->returnSelf());
        $event = new \Slick\Orm\Event\Select(null, ['query' => $query]);
        $this->belongsTo->beforeSelect($event);
    }

    /**
     * Gets the query object mocked
     *
     * @return MockObject|Select
     */
    protected function getQueryMock()
    {
        /** @var Select|MockObject $query */
        $query = $this->getMockBuilder(Select::class)
            ->setConstructorArgs(['people'])
            ->setMethods(['join'])
            ->getMock();
        return $query;
    }

    /**
     * Should grab the modified data from raw source and create an entity to
     * associate to the parent entity
     * @test
     */
    public function afterSelect()
    {
        $data = [
            [
                'users_uid' => 123,
                'users_name' => 'Ana',
                'id' => 1,
                'email' => 'ana@example.com',
                'user_id' => 123
            ]
        ];
        $entityCollection = new EntityCollection();
        $entityCollection->add(new Profile(['id' => 1, 'email' => 'ana@example.com']));
        $event = new \Slick\Orm\Event\Select(null, ['entityCollection' => $entityCollection, 'data' => $data]);
        $event->setAction(\Slick\Orm\Event\Select::ACTION_AFTER_SELECT);
        $this->belongsTo->afterSelect($event);
        $this->assertInstanceOf(Person::class, $entityCollection[0]->person);
    }

    public function testLazyLoad()
    {
        $this->belongsTo->lazyLoaded = true;
        $event = new \Slick\Orm\Event\Select();
        $this->belongsTo->afterSelect($event);
    }

    public function testSave()
    {
        $person = new Person(['id' => '2', 'name' => 'Ana']);
        $profile = new Profile(['email' => 'ana@axample.com', 'person' => $person]);
        $event = new Save($profile, ['email' => 'ana@axample.com']);
        $this->belongsTo->beforeSave($event);
        $this->assertEquals('2', $event->params['user_id']);
    }

    /**
     * Creates a fake annotation
     *
     * @return \Slick\Orm\Annotations\BelongsTo
     */
    protected function getAnnotation()
    {
        if (null == $this->annotation) {
            $this->annotation = new \Slick\Orm\Annotations\BelongsTo(
                'BelongsTo',
                [
                    Person::class => true
                ]
            );
        }
        return $this->annotation;
    }

    /**
     * Gets a mocked database adapter
     *
     * @return MockObject|AdapterInterface
     */
    protected function getMockedAdapter()
    {
        $class = AdapterInterface::class;
        $methods = get_class_methods($class);
        /** @var AdapterInterface|MockObject $adapter */
        $adapter = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        $adapter->method('getDialect')
            ->willReturn(Dialect::MYSQL);
        return $adapter;
    }
}
