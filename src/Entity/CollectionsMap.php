<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Entity;

use Slick\Cache\Cache;
use Slick\Cache\CacheStorageInterface;
use Traversable;

/**
 * Collections Map store
 *
 * @package Slick\Orm\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CollectionsMap implements CollectionsMapInterface
{

    /**
     * @var CacheStorageInterface
     */
    protected $cache;

    /**
     * Set an entity
     *
     * @param string $collectionId
     * @param EntityCollectionInterface $collection
     *
     * @return self|$this|CollectionsMapInterface
     */
    public function set($collectionId, EntityCollectionInterface $collection)
    {
        $this->getCache()->set($collectionId, $collection);
        return $this;
    }

    /**
     * Gets the entity with provided id
     *
     * @param string $collectionId
     *
     * @param null $default
     *
     * @return EntityCollection
     */
    public function get($collectionId, $default = null)
    {
        return $this->getCache()->get($collectionId, $default);
    }

    /**
     * Set cache storage for this identity map
     *
     * @param CacheStorageInterface $cache
     *
     * @return $this|self|CollectionsMap
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