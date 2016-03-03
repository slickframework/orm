<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper;

use Slick\Database\Adapter\AdapterInterface;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\EntityInterface;
use Slick\Orm\EntityMapperInterface;

/**
 * Abstract EntityMapper
 *
 * @package Slick\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class AbstractEntityMapper implements EntityMapperInterface
{

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var EntityDescriptorInterface
     */
    protected $descriptor;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * Get entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getDescriptor()
    {
        if (null == $this->descriptor) {
            $this->setDescriptor(
                EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor($this->getEntityClassName())
            );
        }
        return $this->descriptor;
    }

    /**
     * Set entity descriptor
     *
     * @param EntityDescriptorInterface $descriptor
     *
     * @return $this|self|EntityMapper
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
        return $this;
    }

    /**
     * Sets the adapter for this mapper
     *
     * @param AdapterInterface $adapter
     *
     * @return self|$this|EntityMapper
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
     * Gets entity class name for this mapper
     *
     * @return string
     */
    public function getEntityClassName()
    {
        if (null == $this->entityClassName) {
            $this->setEntityClassName(get_class($this->entity));
        }
        return $this->entityClassName;
    }

    /**
     * Sets entity class name for this mapper
     *
     * @param string $entityClassName
     *
     * @return self|$this|EntityMapper
     */
    public function setEntityClassName($entityClassName)
    {
        $this->entityClassName = $entityClassName;
        return $this;
    }

}