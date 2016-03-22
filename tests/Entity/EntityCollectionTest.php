<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Entity;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Entity\EntityCollection;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * EntityCollection test
 *
 * @package Slick\Tests\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityCollectionTest extends TestCase
{

    /**
     * @var EntityCollection
     */
    protected $entities;

    /**
     * Create the SUT collection object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entities = new EntityCollection(Person::class);
    }

    /**
     * @test
     */
    public function acceptOnlyEntities()
    {
        $this->assertSame(
            $this->entities,
            $this->entities->add(new Person(['id' => 2]))
        );
    }

    /**
     * @test
     */
    public function entitiesAssignAsArray()
    {
        $person = new Person(['id' => 2]);
        $this->entities[] = $person;
        $this->assertSame($person, $this->entities[0]);

    }
}
