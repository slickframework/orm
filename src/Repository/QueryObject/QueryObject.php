<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository\QueryObject;

use League\Event\EmitterInterface;
use Slick\Database\Sql\Select;
use Slick\Orm\Entity\CollectionsMap;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\Delete;
use Slick\Orm\Event\EntityAdded;
use Slick\Orm\Event\EntityChangeEventInterface;
use Slick\Orm\Event\EntityRemoved;
use Slick\Orm\Orm;
use Slick\Orm\RepositoryInterface;

/**
 * QueryObject
 *
 * @package Slick\Orm\Repository
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class QueryObject extends Select implements QueryObjectInterface
{

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var CollectionsMap
     */
    protected $collectionsMap;

    /**
     * For triggering events
     */
    use SelectEventTriggers;

    /**
     * QueryObject has a repository as a dependency.
     *
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->adapter = $repository->getAdapter();
        parent::__construct(
            $repository->getEntityDescriptor()->getTableName(),
            $repository->getEntityDescriptor()->getTableName().'.*'
        );
    }

    /**
     * Retrieve all records matching this select query
     *
     * @return \Slick\Database\RecordList
     */
    public function all()
    {
        return $this->query($this);
    }

    /**
     * Retrieve first record matching this select query
     *
     * @return EntityInterface|null
     */
    public function first()
    {
        $sql = clone($this);
        $sql->limit(1);
        $collection = $this->query($sql);
        return $collection->isEmpty() ? null : $collection[0];
    }

    /**
     * Handles collection add event and updates the cache
     * 
     * @param EntityChangeEventInterface $event
     */
    public function updateCollection(EntityChangeEventInterface $event)
    {
        $collection = $event->getCollection();
        if ($collection->getId()) {
            $collectionMap = $this->getCollectionsMap();
            $collectionMap
                ->set($collection->getId(), $collection);
        }
    }

    /**
     * Execute provided query
     *
     * @param Select $sql
     *
     * @return EntityCollection|EntityInterface|\Slick\Orm\EntityMapperInterface[]
     */
    protected function query(Select $sql)
    {
        $cid = $this->getId($sql);
        $collection = $this->repository
            ->getCollectionsMap()
            ->get($cid, false);

        if (false === $collection) {
            $collection = $this->getCollection($sql, $cid);
        }

        return $collection;
    }

    /**
     * Executes the provided query
     *
     * @param Select $sql
     * @param string $cid
     *
     * @return EntityCollection|EntityInterface|\Slick\Orm\EntityMapperInterface[]
     */
    protected function getCollection(Select $sql, $cid)
    {
        $this->triggerBeforeSelect(
            $sql,
            $this->getRepository()->getEntityDescriptor()
        );
        $data = $this->adapter->query($sql, $sql->getParameters());
        $collection = $this->repository->getEntityMapper()
            ->createFrom($data);
        $this->triggerAfterSelect(
            $data,
            $collection
        );
        $this->registerEventsTo($collection, $cid);
        $this->getCollectionsMap()->set($cid, $collection);
        $this->updateIdentityMap($collection);
        
        return $collection;
    }

    /**
     * Returns the collections map storage
     *
     * @return CollectionsMap|\Slick\Orm\Entity\CollectionsMapInterface
     */
    protected function getCollectionsMap()
    {
        if (null == $this->collectionsMap) {
            $this->collectionsMap = $this->repository->getCollectionsMap();
        }
        return $this->collectionsMap;
    }

    /**
     * Register entity events listeners for the provided collection
     * 
     * @param EntityCollection $collection
     * @param string $cid
     * 
     * @return self
     */
    protected function registerEventsTo(EntityCollection $collection, $cid)
    {
        $collection->setId($cid);
        $collection->getEmitter()
            ->addListener(
                EntityAdded::ACTION_ADD,
                [$this, 'updateCollection']
            );
        $collection->getEmitter()
            ->addListener(
                EntityRemoved::ACTION_REMOVE,
                [$this, 'updateCollection']
            );
        $entity = $this->repository->getEntityDescriptor()->className();
        Orm::addListener(
            $entity,
            Delete::ACTION_AFTER_DELETE,
            function (Delete $event) use ($collection) {
                $collection->remove($event->getEntity());
            },
            EmitterInterface::P_HIGH
        );
        return $this;
    }

    /**
     * Gets the id for this query
     *
     * @param Select $query
     *
     * @return string
     */
    protected function getId(Select $query)
    {
        $str = $query->getQueryString();
        $search = array_keys($query->getParameters());
        $values = array_values($query->getParameters());
        return str_replace($search, $values, $str);
    }

    /**
     * Registers every entity in collection to the repository identity map
     *
     * @param EntityCollection $collection
     *
     * @return self
     */
    protected function updateIdentityMap(EntityCollection $collection)
    {
        if ($collection->isEmpty()) {
            return $this;
        }

        foreach ($collection as $entity)
        {
            $this->repository->getIdentityMap()->set($entity);
        }
        return $this;
    }

    /**
     * Returns the repository that is using this query object
     *
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Gets current entity class name
     *
     * @return string
     */
    public function getEntityClassName()
    {
        return $this->repository->getEntityDescriptor()->className();
    }
}