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
use Slick\Orm\Descriptor\Field\FieldDescriptor;
use Slick\Orm\Event\Select;
use Slick\Orm\Mapper\RelationInterface;
use Slick\Orm\Orm;

/**
 * BelongsTo (Many-To-On) relation
 *
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BelongsTo extends Base implements RelationInterface
{

    /**
     * @readwrite
     * @var string
     */
    protected $property;

    /**
     * @readwrite
     * @var EntityDescriptorInterface
     */
    protected $entityDescriptor;

    /**
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

    public function __construct($options)
    {
        /** @var \Slick\Orm\Annotations\BelongsTo $annotation */
        $annotation = $options['annotation'];
        unset($options['annotation']);
        $options['foreignKey'] = $annotation->getParameter('foreignKey');
        $options['parentEntity'] = $annotation->getValue();
        parent::__construct($options);

        $this->registerListeners();
    }

    /**
     * Sets the parent entity descriptor object
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
     * Gets foreign key name
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

    /**
     * Sets parent entity descriptor
     *
     * @param EntityDescriptorInterface $parentEntityDescriptor
     * @return BelongsTo
     */
    public function setParentEntityDescriptor
    (EntityDescriptorInterface $parentEntityDescriptor
    ) {
        $this->parentEntityDescriptor = $parentEntityDescriptor;
        return $this;
    }

    public function beforeSelect(Select $event)
    {
        $fields = $this->getFieldsPrefixed();
        $table = $this->entityDescriptor->getTableName();
        $relateTable = $this->getParentEntityDescriptor()->getTableName();
        $pmk = $this->getParentEntityDescriptor()->getPrimaryKey()->getField();
        $onClause = "{$table}.{$this->getForeignKey()} = {$relateTable}.{$pmk}";
        $query = $event->getQuery();
        $query->join($relateTable, $onClause, $fields, $relateTable);
    }

    public function afterSelect(Select $event)
    {
        foreach ($event->getEntityCollection() as $index => $entity) {
            $row = $event->getData()[$index];
            $entity->{$this->property} = $this->map($row);
        }
    }

    /**
     * Registers the listener for before select event
     */
    private function registerListeners()
    {
        Orm::addListener(
            $this->entityDescriptor->className(),
            Select::ACTION_BEFORE_SELECT,
            [$this, 'beforeSelect']
        );

        Orm::addListener(
            $this->entityDescriptor->className(),
            Select::ACTION_AFTER_SELECT,
            [$this, 'afterSelect']
        );
    }

    /**
     * Prefixed fields for join
     *
     * @return array
     */
    private function getFieldsPrefixed()
    {
        $fields = $this->getParentEntityDescriptor()->getFields();
        $table = $this->getParentEntityDescriptor()->getTableName();
        $data = [];
        /** @var FieldDescriptor $field */
        foreach ($fields as $field) {
            $data[] = "{$field->getField()} AS ".
                "{$table}_{$field->getField()}";
        }
        return $data;
    }

    private function map($dataRow)
    {
        $relateTable = $this->getParentEntityDescriptor()->getTableName();
        $regexp = "/{$relateTable}_(?P<name>.+)/i";
        $data = [];
        $pmk = $this->getParentEntityDescriptor()->getPrimaryKey()->getField();
        foreach ($dataRow as $field => $value) {
            if (preg_match($regexp, $field, $matched)) {
                $data[$matched['name']] = $value;
            }
        }
        return (isset($data[$pmk]) && $data[$pmk])
            ? Orm::getRepository($this->parentEntity)
                ->getEntityMapper()
                ->createFrom($data)
            : null;
    }

}