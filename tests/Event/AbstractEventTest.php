<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Event;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Event\AbstractEvent;
use Slick\Tests\Orm\Descriptor\Person;

/**
 * AbstractEvent test case
 *
 * @package Slick\Tests\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AbstractEventTest extends TestCase
{

    /**
     * @var AbstractEvent
     */
    protected $event;

    /**
     * Set the SUT event object
     */
    protected function setUp()
    {
        parent::setUp();
        $mike = new Person(['name' => 'Mike']);
        $this->event = $this->getMockBuilder(AbstractEvent::class)
            ->setConstructorArgs([$mike])
            ->getMockForAbstractClass();
    }

    /**
     * Should use the Entity object to get its FQ class name
     * @test
     */
    public function getClassByEntity()
    {
        $this->assertEquals(Person::class, $this->event->getEntityName());
    }
}
