<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository\QueryObject;

use Slick\Database\Sql\Select;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
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
        $cid = $this->getId($this);
        $collection = $this->repository
            ->getCollectionsMap()
            ->get($cid, false);

        if (false === $collection) {
            $this->triggerBeforeSelect(
                $this,
                $this->getRepository()->getEntityDescriptor()
            );
            $data = $this->adapter->query($this, $this->getParameters());
            $collection = $this->repository->getEntityMapper()
                ->createFrom($data);
            $this->triggerAfterSelect(
                $data,
                $collection
            );
            $this->repository->getCollectionsMap()->set($cid, $collection);
            $this->updateIdentityMap($collection);
        }

        return $collection;
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
        $cid = $this->getId($sql);

        $collection = $this->repository
            ->getCollectionsMap()
            ->get($cid, false);

        if (false === $collection) {
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
            $this->repository->getCollectionsMap()->set($cid, $collection);
            $this->updateIdentityMap($collection);
        }

        return $collection->isEmpty() ? null : $collection[0];
    }

    /**
     * Gets the id for this query
     *
     * @param QueryObjectInterface $query
     *
     * @return string
     */
    protected function getId(QueryObjectInterface $query)
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