<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;

use Slick\Common\Base;
use Slick\Common\Utils\Text;
use Slick\Orm\Descriptor\EntityDescriptorInterface;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;

/**
 * AbstractRelation
 *
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class AbstractRelation extends Base
{
    /**
     * @readwrite
     * @var string
     */
    protected $propertyName;

    /**
     * @readwrite
     * @var EntityDescriptorInterface
     */
    protected $entityDescriptor;

    /**
     * Parent or related entity class name
     * @readwrite
     * @var string
     */
    protected $parentEntity;

    /**
     * @readwrite
     * @var EntityDescriptorInterface
     */
    protected $parentEntityDescriptor;

    /**
     * @readwrite
     * @var string
     */
    protected $foreignKey;

    /**
     * Returns the property holding the relation
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }


    /**
     * Gets the entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getEntityDescriptor()
    {
        return $this->entityDescriptor;
    }

    /**
     * Sets entity descriptor
     *
     * @param EntityDescriptorInterface $descriptor
     * @return $this
     */
    public function setEntityDescriptor(EntityDescriptorInterface $descriptor)
    {
        $this->entityDescriptor = $descriptor;
        return $this;
    }

    /**
     * Gets parent entity class name
     *
     * @return string
     */
    public function getParentEntity()
    {
        return $this->parentEntity;
    }

    /**
     * Gets the parent or related entity descriptor
     *
     * @return EntityDescriptorInterface
     */
    public function getParentEntityDescriptor()
    {
        if (is_null($this->parentEntityDescriptor)) {
            $this->setParentEntityDescriptor(
                EntityDescriptorRegistry::getInstance()
                    ->getDescriptorFor($this->parentEntity)
            );
        }
        return $this->parentEntityDescriptor;
    }

    /**
     * Sets parent entity descriptor
     *
     * @param EntityDescriptorInterface $parentEntityDescriptor
     * @return BelongsTo
     */
    public function setParentEntityDescriptor(
        EntityDescriptorInterface $parentEntityDescriptor
    ) {
        $this->parentEntityDescriptor = $parentEntityDescriptor;
        return $this;
    }

    /**
     * Gets the foreign key field name
     *
     * @return string
     */
    public function getForeignKey()
    {
        if (is_null($this->foreignKey)) {
            $name = $this->getParentEntityDescriptor()->getTableName();
            $name = Text::singular(strtolower($name));
            $this->foreignKey = "{$name}_id";
        }
        return $this->foreignKey;
    }
}