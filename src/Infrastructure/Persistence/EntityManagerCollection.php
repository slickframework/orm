<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\Orm\Infrastructure\Persistence;

use ArrayIterator;
use Countable;
use Doctrine\ORM\EntityManagerInterface;
use IteratorAggregate;
use Slick\Orm\Infrastructure\Exception\UnknownEntityManagerException;

/**
 * EntityManagerCollection
 *
 * @package Slick\Orm\Infrastructure\Persistence
 * @implements IteratorAggregate<string, EntityManagerInterface>
 */
final class EntityManagerCollection implements Countable, IteratorAggregate
{
    /** @var array<string, EntityManagerInterface>  */
    private array $entityManagers = [];

    /**
     * Counts the number of entities in the collection.
     *
     * @return int The number of entities in the collection.
     */
    public function count(): int
    {
        return count($this->entityManagers);
    }

    /**
     * Retrieves an iterator for the entities array.
     *
     * @return ArrayIterator<string,EntityManagerInterface> The iterator for the entities array.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->entityManagers);
    }

    /**
     * Adds an instance of EntityManagerInterface to the collection.
     *
     * @param string $name The name of the entity manager.
     * @param EntityManagerInterface $entityManager The entity manager instance to add.
     * @return EntityManagerCollection The updated entity manager collection.
     */
    public function add(string $name, EntityManagerInterface $entityManager): EntityManagerCollection
    {
        $this->entityManagers[$name] = $entityManager;
        return $this;
    }

    public function get(string $name): EntityManagerInterface
    {
        if (!isset($this->entityManagers[$name])) {
            throw new UnknownEntityManagerException(
                "There are no registered entity manager for '$name'"
            );
        }
        return $this->entityManagers[$name];
    }
}
