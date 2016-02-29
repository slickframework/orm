<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Descriptor\EntityDescriptor;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Exception\InvalidArgumentException;
use Slick\Orm\Mapper\EntityMapper;
use Slick\Orm\Mapper\MappersMap;
use Slick\Orm\Repository\EntityRepository;

/**
 * Orm registry
 *
 * @package Slick\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
final class Orm
{

    /**
     * @var MappersMap|EntityMapperInterface[]
     */
    private $mappers;

    /**
     * @var Orm
     */
    private static $instance;

    /**
     * @var AdaptersMap
     */
    private $adapters;

    /**
     * Initialize Orm registry with empty lists
     */
    private function __construct()
    {
        $this->mappers = new MappersMap();
        $this->adapters = new AdaptersMap();
    }

    /**
     * Avoid clone on a singleton
     * @codeCoverageIgnore
     */
    private function __clone()
    {

    }

    /**
     * Gets a ORM registry instance
     *
     * @return Orm
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * Retrieves the mapper for provided entity
     *
     * If mapper does not exists it will be created and stored in the
     * mapper map.
     *
     * @param String $entity
     * @return EntityMapper
     */
    public static function getMapper($entity)
    {
        return self::getInstance()->getMapperFor($entity);
    }

    /**
     * Gets repository for provided entity class name
     *
     * @param string $entityClass FQ entity class name
     *
     * @return EntityRepository
     *
     * @throws InvalidArgumentException If provide class name is not
     *   from a class that implements the EntityInterface interface.
     */
    public static function getRepository($entityClass)
    {
        return self::getInstance()->getRepositoryFor($entityClass);
    }

    /**
     * Gets repository for provided entity class name
     *
     * @param string $entityClass FQ entity class name
     *
     * @return EntityRepository
     *
     * @throws InvalidArgumentException If provide class name is not
     *   from a class that implements the EntityInterface interface.
     */
    public function getRepositoryFor($entityClass)
    {
        if (!is_subclass_of($entityClass, EntityInterface::class)) {
            throw new InvalidArgumentException(
                'Cannot create ORM repository for a class that does not '.
                'implement EntityInterface.'
            );
        }
        $repository = new EntityRepository();
        $repository->setAdapter(
            $this->adapters->get($this->getAdapterAlias($entityClass))
        )
            ->setEntityMapper($this->getMapperFor($entityClass))
            ->setEntityDescriptor(EntityDescriptorRegistry::getInstance()
            ->getDescriptorFor($entityClass));
        return $repository;
    }

    /**
     * Retrieves the mapper for provided entity
     *
     * If mapper does not exists it will be created and stored in the
     * mapper map.
     *
     * @param string $entity
     * @return EntityMapper
     */
    public function getMapperFor($entity)
    {
        return  $this->mappers->containsKey($entity)
            ? $this->mappers->get($entity)
            : $this->createMapper($entity);
    }

    /**
     * Sets default adapter
     *
     * @param AdapterInterface $adapter
     * @return $this|Orm|self
     */
    public function setDefaultAdapter(AdapterInterface $adapter)
    {
        return $this->setAdapter('default', $adapter);
    }

    /**
     * Sets an adapter mapped with alias name
     *
     * @param string $alias
     * @param AdapterInterface $adapter
     *
     * @return $this|Orm|self
     */
    public function setAdapter($alias, AdapterInterface $adapter)
    {
        $this->adapters->set($alias, $adapter);
        return $this;
    }

    /**
     * Creates entity map for provided entity
     *
     * @param string $entity
     * @return EntityMapper
     */
    private function createMapper($entity)
    {
        $mapper = new EntityMapper();
        $mapper->setAdapter(
            $this->adapters->get(
                $this->getAdapterAlias($entity)
            )
        )
            ->setEntity($entity);
        $this->mappers->set($entity, $mapper);
        return $mapper;
    }

    /**
     * Gets the adapter alias for current working entity
     *
     * @param string $entity
     *
     * @return EntityDescriptor|string
     */
    private function getAdapterAlias($entity)
    {
        $descriptor = EntityDescriptorRegistry::getInstance()
            ->getDescriptorFor($entity);
        return $descriptor->getAdapterAlias()
            ? $descriptor->getAdapterAlias()
            : 'default';
    }
}