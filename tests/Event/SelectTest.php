<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Orm\Event;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Orm\Event\Select;

/**
 * Select test case
 *
 * @package Slick\Tests\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class SelectTest extends TestCase
{

    /**
     * @var Select
     */
    protected $event;

    /**
     * Sets the SUT or tests
     */
    protected function setUp()
    {
        parent::setUp();
        $this->event = new Select();
    }

    public function testGetQuery()
    {
        $this->assertNull($this->event->getQuery());
    }

    public function testGetData()
    {
        $this->assertNull($this->event->getData());
    }

    public function testGetEntityCollection()
    {
        $this->assertNull($this->event->getEntityCollection());
    }
}
