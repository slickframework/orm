<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;

use Slick\Common\Utils\Text;
use Slick\Database\Sql;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\Delete;
use Slick\Orm\Event\Select;
use Slick\Orm\Orm;

/**
 * HasOne (one-to-one) relation
 * 
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasOne extends BelongsTo
{

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
        $pmk = $this->entityDescriptor->getPrimaryKey()->getField();

        $onClause = "{$table}.{$pmk} = ".
            "{$relateTable}.{$this->getForeignKey()}";

        $query = $event->getQuery();
        $query->join($relateTable, $onClause, $fields, $relateTable);
    }

    /**
     * Gets the foreign key field name
     *
     * @return string
     */
    public function getForeignKey()
    {
        if (is_null($this->foreignKey)) {
            $name = $this->getEntityDescriptor()->getTableName();
            $this->foreignKey = "{$this->normalizeFieldName($name)}_id";
        }
        return $this->foreignKey;
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
        $pmk = $this->getEntityDescriptor()->getPrimaryKey();
        $fnk = $this->getForeignKey();

        $data = Sql::createSql($adapter)
            ->select($relTable)
            ->where([
                "{$relTable}.{$fnk} = :id" => [
                    ':id' => $entity->{$pmk->name}
                ]
            ])
            ->first();

        $relEntity = $this->getParentEntityMapper()->createFrom($data);

        return null == $relEntity ? null :$this->registerEntity($relEntity);
    }

    /**
     * Deletes the related entity for before deleting current entity
     * 
     * @param Delete $event
     */
    public function beforeDelete(Delete $event)
    {
        $parent = $event->getEntity()->{$this->propertyName};
        if ($parent instanceof EntityInterface) {
            $parent->delete();
        }
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
            Delete::ACTION_BEFORE_DELETE,
            [$this, 'beforeDelete']
        );
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
        $tableName = $this->getParentTableName();
        $pmk = $this->getParentPrimaryKey();
        $index = "{$tableName}_{$pmk}";

        // No join data on results
        if (! array_key_exists($index, $dataRow)) {
            return false;
        }

        $entity = $this->getParentRepository()
            ->getIdentityMap()
            ->get($dataRow[$index], false);

        if (false === $entity) {
            $entity = $this->map($dataRow);
        }
        return $entity;
    }
    
}