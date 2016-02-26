<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor;

use Slick\Orm\EntityInterface;

/**
 * Entity Descriptor Registry
 *
 * @package Slick\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
final class EntityDescriptorRegistry
{

    /**
     * @var DescriptorsCollection
     */
    private $descriptors;

    /**
     * @var EntityDescriptorRegistry
     */
    private static $instance;

    /**
     * Singleton enforce constructor
     */
    private function __construct()
    {
        $this->descriptors = new DescriptorsCollection();
    }

    /**
     * Avoid singleton clone
     * @codeCoverageIgnore
     */
    private function __clone()
    {
        // Singleton cannot be cloned.
    }

    /**
     * Get running instance of Entity Descriptor Registry
     *
     * @return EntityDescriptorRegistry
     */
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Returns the descriptor for provided class name
     *
     * @param EntityInterface $entity
     *
     * @return EntityDescriptor
     */
    public function getDescriptorFor(EntityInterface $entity)
    {
        $key = get_class($entity);
        return $this->descriptors->containsKey($key)
            ? $this->descriptors->get($key)
            : $this->createDescriptor($key);
    }

    /**
     * Creates an entity descriptor for provided class name
     *
     * The create descriptor is stored in the map
     *
     * @param string $entityClass
     *
     * @return EntityDescriptor
     */
    private function createDescriptor($entityClass)
    {
        $descriptor = new EntityDescriptor($entityClass);
        $this->descriptors->set($entityClass, $descriptor);
        return $descriptor;
    }

}