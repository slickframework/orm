<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

use Slick\Cache\Cache;
use Slick\Cache\CacheStorageInterface;
use Slick\Orm\EntityInterface;

/**
 * Identity Map
 *
 * Ensures that each object gets loaded only once by keeping every loaded
 * object in a map. Looks up objects using the map when referring to them.
 *
 * @package Slick\Orm\Repository
 */
class IdentityMap implements IdentityMapInterface
{

    /**
     * @var CacheStorageInterface
     */
    protected $cache;

    /**
     * Set an entity
     *
     * @param EntityInterface $entity
     *
     * @return self|$this|IdentityMapInterface
     */
    public function set(EntityInterface $entity)
    {
        $this->getCache()->set($entity->getId(), $entity);
        return $this;
    }

    /**
     * Gets the entity with provided id
     *
     * @param mixed $entityId
     *
     * @param null $default
     *
     * @return null|mixed|EntityInterface
     */
    public function get($entityId, $default = null)
    {
        return $this->getCache()->get($entityId, $default);

    }

    /**
     * Remove an entity from identity map
     *
     * @param mixed $entityId
     *
     * @return self|$this|IdentityMapInterface
     */
    public function remove($entityId)
    {
        $this->getCache()->erase($entityId);
        return $this;
    }

    /**
     * Set cache storage for this identity map
     *
     * @param CacheStorageInterface $cache
     *
     * @return $this|self|IdentityMap
     */
    public function setCache(CacheStorageInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Gets cache storage for this identity map
     *
     * @return CacheStorageInterface
     */
    public function getCache()
    {
        if (null == $this->cache) {
            $this->setCache(Cache::get(Cache::CACHE_MEMORY));
        }
        return $this->cache;
    }
}