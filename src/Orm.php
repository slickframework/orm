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
use Slick\Orm\Mapper\EntityMapper;
use Slick\Orm\Mapper\MappersMap;

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
     * @param EntityInterface $entity
     * @return EntityMapper
     */
    public static function mapperFor(EntityInterface $entity)
    {
        return self::getInstance()->getMapperFor($entity);
    }

    /**
     * Retrieves the mapper for provided entity
     *
     * If mapper does not exists it will be created and stored in the
     * mapper map.
     *
     * @param EntityInterface $entity
     * @return EntityMapper
     */
    public function getMapperFor(EntityInterface $entity)
    {
        $class = get_class($entity);
        return  $this->mappers->containsKey($class)
            ? $this->mappers->get($class)
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
     * @param EntityInterface $entity
     * @return EntityMapper
     */
    private function createMapper(EntityInterface $entity)
    {
        $class = get_class($entity);
        $mapper = new EntityMapper();
        $mapper->setAdapter(
            $this->adapters->get(
                $this->getAdapterAlias($entity)
            )
        );
        $this->mappers->set($class, $mapper);
        return $mapper;
    }

    /**
     * Gets the adapter alias for current working entity
     *
     * @param EntityInterface $entity
     *
     * @return EntityDescriptor|string
     */
    private function getAdapterAlias(EntityInterface $entity)
    {
        $descriptor = EntityDescriptorRegistry::getInstance()
            ->getDescriptorFor($entity);
        return $descriptor->getAdapterAlias()
            ? $descriptor->getAdapterAlias()
            : 'default';
    }
}