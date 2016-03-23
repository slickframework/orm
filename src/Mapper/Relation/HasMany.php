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
use Slick\Orm\Entity\EntityCollectionInterface;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\EntityAdded;
use Slick\Orm\Mapper\RelationInterface;

/**
 * HasMany
 *
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasMany extends AbstractRelation implements RelationInterface
{
    /**
     * Relations utility methods
     */
    use RelationsUtilityMethods;

    /**
     * @readwrite
     * @var integer
     */
    protected $limit;

    /**
     * @readwrite
     * @var string
     */
    protected $order;

    /**
     * @readwrite
     * @var mixed
     */
    protected $conditions;

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
        $options['limit'] = $annotation->getParameter('limit');
        $options['order'] = $annotation->getParameter('order');
        $options['conditions'] = $annotation->getParameter('conditions');

        parent::__construct($options);
    }

    /**
     * Loads the entity or entity collection for this relation
     *
     * @param EntityInterface $entity
     *
     * @return null|EntityInterface|EntityCollection|EntityInterface[]
     */
    public function load(EntityInterface $entity)
    {
        $repository = $this->getParentRepository();
        /** @var EntityCollectionInterface $collection */
        $collection = $repository->find()
            ->where($this->getConditions($entity))
            ->limit($this->limit)
            ->all();
        $collection
            ->setParentEntity($entity)
            ->getEmitter()
            ->addListener(EntityAdded::ACTION_ADD, [$this, 'add']);

        return $collection;
    }

    /**
     * Saves the relation foreign key upon entity add
     * 
     * @param EntityAdded $event
     */    
    public function add(EntityAdded $event)
    {
        $entity = $event->getEntity();
        $table = $this->getParentTableName();
        $pmk = $this->getParentPrimaryKey();
        $value = $event->getCollection()->parentEntity()->getId();
        Sql::createSql($this->getAdapter())
            ->update($table)
            ->set([$this->getForeignKey() => $value])
            ->where(["{$pmk} = :id" => [':id' => $entity->getId()]])
            ->execute();
    }

    /**
     * Gets the relation conditions
     *
     * @param EntityInterface $entity
     * @return array
     */
    protected function getConditions(EntityInterface $entity)
    {
        $field = "{$this->getParentTableName()}.{$this->getForeignKey()}";
        $property = $this->getPropertyName();
        $conditions = [
            "{$field} = :{$property}" => [
                ":{$property}" => $entity->getId()
            ]
        ];
        return $conditions;
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
            $name = Text::singular(strtolower($name));
            $this->foreignKey = "{$name}_id";
        }
        return $this->foreignKey;
    }
}