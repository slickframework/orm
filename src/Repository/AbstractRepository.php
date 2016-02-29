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

}