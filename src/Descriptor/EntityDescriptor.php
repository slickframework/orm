<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor;

use Slick\Common\Inspector;
use Slick\Common\Utils\Text;
use Slick\Orm\Annotations\Column;
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\Descriptor\Field\FieldsCollection;

/**
 * Entity Descriptor
 *
 * @package Slick\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityDescriptor implements EntityDescriptorInterface
{

    /**
     * @var string Entity class name
     */
    protected $entity;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var Inspector
     */
    protected $inspector;

    /**
     * @var FieldsCollection
     */
    protected $fields;

    /**
     * @var FieldDescriptor
     */
    protected $primaryKey;

    /**
     * @var string
     */
    protected $adapterAlias = '__undefined__';

    /**
     * EntityDescriptor need an entity FQ class name.
     *
     * @param string $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->inspector = Inspector::forClass($entity);
    }

    /**
     * Gets entity table name
     *
     * @return string
     */
    public function getTableName()
    {
        if (null == $this->tableName) {
            $this->tableName = $this->determineTableName();
        }
        return $this->tableName;
    }

    /**
     * Returns entity fields
     *
     * @return FieldsCollection|FieldDescriptor[]
     */
    public function getFields()
    {
        if (null == $this->fields) {
            $properties = $this->inspector->getClassProperties();
            $this->fields = new FieldsCollection();
            foreach ($properties as $property) {
                $this->addDescriptor($property);
            }
        }
        return $this->fields;
    }

    /**
     * Returns the primary key field
     *
     * @return FieldDescriptor|null
     */
    public function getPrimaryKey()
    {
        if (null == $this->primaryKey) {
            foreach ($this->getFields() as $field) {
                if ($field->isPrimaryKey()) {
                    $this->primaryKey = $field;
                    break;
                }
            }
        }
        return $this->primaryKey;
    }

    /**
     * Determines the table name for current entity
     *
     * If there is an annotation @table present it will be used
     * otherwise the name will be parsed by convention using the
     * EntityDescriptor::parseTableName() method.
     *
     * @return string
     */
    private function determineTableName()
    {
        $annotations = $this->inspector->getClassAnnotations();
        $name = self::parseTableName($this->entity);
        if ($annotations->hasAnnotation('@table')) {
            $name = $annotations->getAnnotation('@table')->getValue();
        }
        return $name;
    }

    /**
     * Creates the descriptor if provided property has annotation @column
     *
     * @param $property
     *
     * @return self|$this|EntityDescriptor
     */
    private function addDescriptor($property)
    {
        $annotations = $this->inspector
            ->getPropertyAnnotations($property);
        if ($annotations->hasAnnotation('column')) {
            /** @var Column $annotation */
            $annotation = $annotations->getAnnotation('column');
            $descriptor = new FieldDescriptor($annotation->getParameters());
            $this->fields[] = $descriptor->setName($property);
        }
        return $this;
    }

    /**
     * Parses the table name from the class name
     *
     * @param string $className
     *
     * @return string
     */
    public static function parseTableName($className)
    {
        $parts = explode('\\', $className);
        $name = end($parts);
        $tableName = null;

        $words = explode('#', Text::camelCaseToSeparator($name, "#"));
        $last = array_pop($words);
        $last = Text::plural(strtolower($last));
        array_push($words, $last);
        foreach ($words as $word) {
            $tableName .= ucfirst($word);
        }
        return lcfirst($tableName);
    }

    /**
     * Returns the adapter alias name to use with this entity
     *
     * @return string
     */
    public function getAdapterAlias()
    {
        if ('__undefined__' == $this->adapterAlias) {
            $this->adapterAlias = null;
            $annotations = $this->inspector->getClassAnnotations();
            if ($annotations->hasAnnotation('@adapter')) {
                $this->adapterAlias = $annotations
                    ->getAnnotation('@adapter')
                    ->getValue();
            }
        }
        return $this->adapterAlias;
    }

    /**
     * Gets entity class name
     *
     * @return string
     */
    public function className()
    {
        return $this->entity;
    }
}