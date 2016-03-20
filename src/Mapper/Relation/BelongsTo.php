<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;

use Slick\Database\Sql;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\Save;
use Slick\Orm\Event\Select;
use Slick\Orm\Mapper\RelationInterface;
use Slick\Orm\Orm;

/**
 * BelongsTo (Many-To-On) relation
 *
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BelongsTo extends AbstractRelation implements RelationInterface
{
    /**
     * Relations utility methods
     */
    use RelationsUtilityMethods;

    /**
     * @readwrite
     * @var bool
     */
    protected $dependent = true;

    /**
     * BelongsTo relation
     *
     * @param array|object $options The parameters from annotation
     */
    public function __construct($options)
    {
        /** @var \Slick\Orm\Annotations\BelongsTo $annotation */
        $annotation = $options['annotation'];
        unset($options['annotation']);
        $options['foreignKey'] = $annotation->getParameter('foreignKey');
        $options['parentEntity'] = $annotation->getValue();
        $options['lazyLoaded'] = $annotation->getParameter('lazyLoaded');

        parent::__construct($options);

        $this->registerListeners();
    }

    /**
     * Handles the before select callback
     *
     * @param Select $event
     */
    public function beforeSelect(Select $event)
    {
        if ($this->isLazyLoaded()) {
            return;
        }

        $fields = $this->getFieldsPrefixed();
        $table = $this->entityDescriptor->getTableName();
        $relateTable = $this->getParentTableName();
        $pmk = $this->getParentPrimaryKey();

        $onClause = "{$table}.{$this->getForeignKey()} = ".
            "{$relateTable}.{$pmk}";

        $query = $event->getQuery();
        $query->join($relateTable, $onClause, $fields, $relateTable);
    }

    /**
     * Handles the after select callback
     *
     * @param Select $event
     */
    public function afterSelect(Select $event)
    {
        if ($this->isLazyLoaded()) {
            return;
        }
        foreach ($event->getEntityCollection() as $index => $entity) {
            $row = $event->getData()[$index];
            $entity->{$this->propertyName} = $this->getFromMap($row);
        }
    }

    public function beforeSave(Save $event)
    {
        $parent = $event->getEntity()->{$this->propertyName};
        if ($parent instanceof EntityInterface) {
            $parent = $parent->getId();
        }
        $event->params[$this->getForeignKey()] = $parent;
        $related = $this->getParentRepository()
            ->get($parent);
        $event->getEntity()->{$this->propertyName} = $related;
    }

    /**
     * Registers the listener for before select event
     */
    protected function registerListeners()
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

        Orm::addListener(
            $this->entityDescriptor->className(),
            Save::ACTION_BEFORE_INSERT,
            [$this, 'beforeSave']
        );

        Orm::addListener(
            $this->entityDescriptor->className(),
            Save::ACTION_BEFORE_UPDATE,
            [$this, 'beforeSave']
        );
    }

    /**
     * Prefixed fields for join
     *
     * @return array
     */
    protected function getFieldsPrefixed()
    {
        $table = $this->getParentTableName();
        $data = [];

        foreach ($this->getParentFields() as $field) {
            $data[] = "{$field->getField()} AS ".
                "{$table}_{$field->getField()}";
        }
        return $data;
    }

    /**
     * Check if entity is already loaded and uses it.
     *
     * If not loaded the entity will be created and loaded to the repository's
     * identity map so that it can be reused next time.
     *
     * @param array $dataRow
     *
     * @return null|EntityCollection|EntityInterface|EntityInterface[]
     */
    protected function getFromMap($dataRow)
    {
        $entity = $this->getParentRepository()
            ->getIdentityMap()
            ->get($dataRow[$this->getForeignKey()], false);
        if (false === $entity) {
            $entity = $this->map($dataRow);
        }
        return $entity;
    }

    /**
     * Creates and maps related entity
     *
     * @param array $dataRow
     *
     * @return null|EntityCollection|EntityInterface|EntityInterface[]
     */
    protected function map($dataRow)
    {
        $data = $this->getData($dataRow);
        $pmk = $this->getParentPrimaryKey();
        $entity = (isset($data[$pmk]) && $data[$pmk])
            ? $this->getParentEntityMapper()->createFrom($data)
            : null;
        return null == $entity ? null : $this->registerEntity($entity);
    }

    /**
     * Gets a data array with fields and values for parent entity creation
     *
     * @param array $dataRow
     *
     * @return array
     */
    protected function getData($dataRow)
    {
        $data = [];
        $relateTable = $this->getParentTableName();
        $regexp = "/{$relateTable}_(?P<name>.+)/i";
        foreach ($dataRow as $field => $value) {
            if (preg_match($regexp, $field, $matched)) {
                $data[$matched['name']] = $value;
            }
        }
        return $data;
    }

    /**
     * Loads the entity or entity collection for this relation
     *
     * @param EntityInterface $entity
     *
     * @return null|EntityInterface
     */
    public function load(EntityInterface $entity)
    {
        $adapter = $this->getAdapter();

        $relTable = $this->getParentTableName();
        $relPmk = $this->getParentPrimaryKey();

        $table = $this->getEntityDescriptor()->getTableName();
        $pmk = $this->getEntityDescriptor()->getPrimaryKey();
        $fnk = $this->getForeignKey();

        $onClause = "{$relTable}.{$relPmk} = {$table}.{$fnk}";

        $data = Sql::createSql($adapter)
            ->select($relTable)
            ->join($table, $onClause, null)
            ->where([
                "{$table}.{$pmk->getField()} = :id" => [
                    ':id' => $entity->{$pmk->getName()}
                ]
            ])
            ->first();

        $relEntity = $this->getParentEntityMapper()->createFrom($data);

        return null == $relEntity ? null :$this->registerEntity($relEntity);
    }
}