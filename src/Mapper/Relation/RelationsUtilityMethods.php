<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;

use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\Descriptor\Field\FieldsCollection;
use Slick\Orm\Orm;

/**
 * Useful methods for relations
 *
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait RelationsUtilityMethods
{

    /**
     * Gets the parent or related entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    abstract public function getParentEntityDescriptor();

    /**
     * Gets parent entity class name
     *
     * @return string
     */
    abstract public function getParentEntity();

    /**
     * Gets the parent entity primary key field name
     *
     * @return string
     */
    public function getParentPrimaryKey()
    {
        return $this->getParentEntityDescriptor()
            ->getPrimaryKey()
            ->getField();
    }

    /**
     * Gets parent entity repository
     *
     * @return \Slick\Orm\Repository\EntityRepository
     */
    public function getParentRepository()
    {
        return Orm::getRepository($this->getParentEntity());
    }

    /**
     * Gets the entity mapper of parent entity repository
     *
     * @return \Slick\Orm\EntityMapperInterface
     */
    public function getParentEntityMapper()
    {
        return $this->getParentRepository()
            ->getEntityMapper();
    }

    /**
     * Gets parent entity table name
     *
     * @return string
     */
    public function getParentTableName()
    {
        return $this->getParentEntityDescriptor()
            ->getTableName();
    }

    /**
     * Get parent entity fields collection
     *
     * @return FieldsCollection|FieldDescriptor[]
     */
    public function getParentFields()
    {
        return $this->getParentEntityDescriptor()
            ->getFields();
    }
}