<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Repository;

use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\EntityMapperInterface;

/**
 * Abstract entity Repository
 *
 * @package Slick\Orm
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class AbstractRepository
{

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var EntityDescriptorInterface
     */
    protected $entityDescriptor;

    /**
     * @var EntityMapperInterface
     */
    protected $entityMapper;

    /**
     * @var IdentityMapInterface
     */
    protected $identityMap;

    /**
     * Sets the adapter for this statement
     *
     * @param AdapterInterface $adapter
     *
     * @return $this|self|AbstractRepository
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Retrieves the current adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set the entity descriptor interface
     *
     * @param EntityDescriptorInterface $descriptor
     * @return $this|self|AbstractRepository
     */
    public function setEntityDescriptor(EntityDescriptorInterface $descriptor)
    {
        $this->entityDescriptor = $descriptor;
        return $this;
    }

    /**
     * Gets entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getEntityDescriptor()
    {
        return $this->entityDescriptor;
    }

    /**
     * @return EntityMapperInterface
     */
    public function getEntityMapper()
    {
        return $this->entityMapper;
    }

    /**
     * @param $entityMapper
     * @return $this
     */
    public function setEntityMapper($entityMapper)
    {
        $this->entityMapper = $entityMapper;
        return $this;
    }

    /**
     * Sets identity map for this repository
     *
     * @param IdentityMapInterface $map
     * @return $this|self|EntityRepository
     */
    public function setIdentityMap(IdentityMapInterface $map)
    {
        $this->identityMap = $map;
        return $this;
    }

    /**
     * Gets identity map for this repository
     *
     * @return IdentityMapInterface
     */
    public function getIdentityMap()
    {
        if (null == $this->identityMap) {
            $this->setIdentityMap(new IdentityMap());
        }
        return $this->identityMap;
    }

}