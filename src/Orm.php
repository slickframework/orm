<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use League\Event\ListenerInterface;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Descriptor\EntityDescriptor;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Event\EmittersMap;
use Slick\Orm\Exception\InvalidArgumentException;
use Slick\Orm\Mapper\EntityMapper;
use Slick\Orm\Mapper\MappersMap;
use Slick\Orm\Repository\EntityRepository;
use Slick\Orm\Repository\RepositoryMap;

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
     * @var RepositoryMap
     */
    private $repositories;

    /**
     * @var EmittersMap
     */
    private $emitters;

    /**
     * Initialize Orm registry with empty lists
     */
    private function __construct()
    {
        $this->mappers = new MappersMap();
        $this->adapters = new AdaptersMap();
        $this->repositories = new RepositoryMap();
        $this->emitters = new EmittersMap();
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
     * Gets event emitter for provided entity class
     *
     * @param string $entityClass FQ entity class name
     *
     * @return Emitter
     */
    public static function getEmitter($entityClass)
    {
        return self::getInstance()->getEmitterFor($entityClass);
    }

    /**
     * Sets the emitter for provided class name
     *
     * @param string $entityClass
     * @param EmitterInterface $emitter
     * @return $this|self|Orm
     */
    public function setEmitter($entityClass, EmitterInterface $emitter)
    {
        $this->emitters->set($entityClass, $emitter);
        return $this;
    }

    /**
     * Add a listener for an entity event.
     *
     * The $event parameter should be the event name, and the second should be
     * the event listener. It may implement the League\Event\ListenerInterface
     * or simply be "callable". In this case, the priority emitter also accepts
     * an optional third parameter specifying the priority as an integer. You
     * may use one of EmitterInterface predefined constants here if you want.
     *
     * @param string|EntityInterface     $entityClass
     * @param string                     $event
     * @param ListenerInterface|callable $listener
     * @param int                        $priority
     *
     * @return EmitterInterface
     */
    public static function addListener(
        $entityClass, $event, $listener, $priority = EmitterInterface::P_NORMAL
    ) {
        return self::getInstance()->addListenerFor($entityClass,$event, $listener, $priority);
    }

    /**
     * Add a listener for an entity event.
     *
     * The $event parameter should be the event name, and the second should be
     * the event listener. It may implement the League\Event\ListenerInterface
     * or simply be "callable". In this case, the priority emitter also accepts
     * an optional third parameter specifying the priority as an integer. You
     * may use one of EmitterInterface predefined constants here if you want.
     *
     * @param string|EntityInterface     $entityClass
     * @param string                     $event
     * @param ListenerInterface|callable $listener
     * @param int                        $priority
     *
     * @return EmitterInterface
     */
    public function addListenerFor(
        $entityClass, $event, $listener, $priority = EmitterInterface::P_NORMAL
    ) {
        $className = is_object($entityClass)
            ? get_class($entityClass)
            : $entityClass;
        return $this->getEmitterFor($className)
            ->addListener($event, $listener, $priority);
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

        return $this->repositories->containsKey($entityClass)
            ? $this->repositories->get($entityClass)
            : $this->createRepository($entityClass);
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
     * Gets the event emitter for provided entity
     *
     * @param string $entity
     *
     * @return Emitter
     */
    public function getEmitterFor($entity)
    {
        return $this->emitters->containsKey($entity)
            ? $this->emitters->get($entity)
            : $this->createEmitter($entity);
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
     * Creates a repository for provided entity class name
     *
     * @param string $entityClass
     * @return EntityRepository
     */
    private function createRepository($entityClass)
    {
        $repository = new EntityRepository();
        $repository->setAdapter(
            $this->adapters->get($this->getAdapterAlias($entityClass))
        )
            ->setEntityMapper($this->getMapperFor($entityClass))
            ->setEntityDescriptor(EntityDescriptorRegistry::getInstance()
                ->getDescriptorFor($entityClass));
        $this->repositories->set($entityClass, $repository);
        return $repository;
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
            ->setEntityClassName($entity);
        $this->mappers->set($entity, $mapper);
        return $mapper;
    }

    /**
     * Creates an emitter for provided entity class name
     *
     * @param string $entity
     * @return Emitter
     */
    private function createEmitter($entity)
    {
        $emitter = new Emitter();
        $this->emitters->set($entity, $emitter);
        return $emitter;
    }

    /**
     * Gets the adapter alias for current working entity
     *
     * @param string $entity
     *
     * @return string
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