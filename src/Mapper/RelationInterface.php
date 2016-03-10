<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper;

use Slick\Orm\Descriptor\EntityDescriptorInterface;

/**
 * RelationInterface defines a relation between two entities
 *
 * @package Slick\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface RelationInterface
{

    /**
     * Returns the property holding the relation
     *
     * @return string
     */
    public function getPropertyName();


    /**
     * Gets the entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getEntityDescriptor();

    /**
     * Gets parent entity class name
     *
     * @return string
     */
    public function getParentEntity();

    /**
     * Gets the parent or related entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getParentEntityDescriptor();

    /**
     * Gets the foreign key field name
     *
     * @return string
     */
    public function getForeignKey();
}