<?php
/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Slick\Orm\Infrastructure\Persistence;

use ArrayIterator;
use Countable;
use Doctrine\ORM\EntityManagerInterface;
use IteratorAggregate;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\Orm\Infrastructure\Exception\UnknownEntityManagerException;
use Slick\Orm\Infrastructure\Persistence\EntityManagerCollection;
use PHPUnit\Framework\TestCase;

class EntityManagerCollectionTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $collection = new EntityManagerCollection();
        $this->assertInstanceOf(EntityManagerCollection::class, $collection);
    }

    #[Test]
    public function itsCountable(): void
    {
        $collection = new EntityManagerCollection();
        $this->assertInstanceOf(Countable::class, $collection);
        $this->assertCount(0, $collection);
    }

    #[Test]
    public function itsAnIteratorAggregate(): void
    {
        $collection = new EntityManagerCollection();
        $this->assertInstanceOf(IteratorAggregate::class, $collection);
        $this->assertInstanceOf(ArrayIterator::class, $collection->getIterator());
    }

    #[Test]
    public function addEntity(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class)->reveal();
        $collection = new EntityManagerCollection();
        $this->assertSame($collection, $collection->add('my-entity', $entityManager));
        $this->assertSame($entityManager, $collection->get('my-entity'));
    }

    #[Test]
    public function exceptionOnMissingName(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class)->reveal();
        $collection = new EntityManagerCollection();
        $this->assertSame($collection, $collection->add('my-entity', $entityManager));
        $this->assertCount(1, $collection);
        $this->expectException(UnknownEntityManagerException::class);
        $manager = $collection->get('other-entity');
        $this->assertNull($manager);
    }
}
