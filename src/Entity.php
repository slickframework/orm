<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm;

use Slick\Common\Base;
use Slick\Common\Exception\WriteOnlyException;
use Slick\Common\Inspector;
use Slick\Orm\Descriptor\EntityDescriptorRegistry;
use Slick\Orm\Descriptor\Field\FieldDescriptor;

/**
 * Entity
 *
 * @package Slick\Orm
 * @readwrite
 */
abstract class Entity extends Base implements EntityInterface
{

    /**
     * Saves current entity state
     *
     * Optionally saves only the partial data if $data argument is passed. If
     * no data is given al the field properties will be updated.
     *
     * @param array $data Partial data to save
     *
     * @return mixed
     */
    public function save(array $data = [])
    {
        foreach ($data as $property => $value) {
            $this->{$property} = $value;
        }
        return $this->getMapper()
            ->save($this);

    }

    /**
     * Deletes current entity from its storage
     *
     * @return self|$this|EntityInterface
     */
    public function delete()
    {
        return $this->getMapper()
            ->delete($this);
    }

    /**
     * Retrieves the data mapper for this entity
     */
    public function getMapper()
    {
        return Orm::getMapper(get_class($this));
    }

    /**
     * Returns the entity fields as a key/value associative array
     *
     * @return array
     */
    public function asArray()
    {
        $inspector = Inspector::forClass($this);
        $properties = $inspector->getClassProperties();
        $data = [];
        foreach ($properties as $property) {
            $annotations = $inspector->getPropertyAnnotations($property);
            if (
                $annotations->hasAnnotation('readwrite') ||
                $annotations->hasAnnotation('read')
            ) {
                $data[$property] = $inspector->getReflection()
                    ->getProperty($property)
                    ->getValue();
            }
        }
    }

    /**
     * Returns a string representation for this entity
     *
     * @return string
     */
    public function __toString()
    {
        $field = $this->getMapper()->getDescriptor()->getDisplayFiled();
        $value = $this->{$field->getName()};
        return (string) $value;
    }

    /**
     * Retrieves the value of a property with the given name.
     *
     * @param string $name The property name where to get the value from.
     *
     * @return mixed The property value.
     *
     * @throws WriteOnlyException If the property being accessed
     * has the annotation @write
     */
    protected function getter($name)
    {
        $value = parent::getter($name);
        $name = $this->getProperty($name);
        if (null == $value) {
            $relMap = EntityDescriptorRegistry::getInstance()
                ->getDescriptorFor(get_class($this))
                ->getRelationsMap();
            if ($relMap->containsKey($name)) {
                $value = $relMap->get($name)->load($this);
            }
        }
        return $value;
    }

    /**
     * Retrieve the property name
     *
     * This method was designed to support old framework normalization
     * with the "_" underscore prefix character on property names.
     * The "_" should not be used in the PSR-2 standard
     *
     * @param string $name
     *
     * @return string|false
     */
    private function getProperty($name)
    {
        $normalized = lcfirst($name);
        $old = "_{$normalized}";
        $name = false;
        foreach ($this->getInspector()->getClassProperties() as $property) {
            if ($old == $property || $normalized == $property) {
                $name = $property;
                break;
            }
        }
        return $name;
    }

}