<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

use \League\Event\AbstractEvent as LeagueAbstractEvent;
use Slick\Orm\EntityInterface;

/**
 * Abstract ORM Event
 *
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class AbstractEvent extends LeagueAbstractEvent implements EventInterface
{

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var string
     */
    protected $action = 'none';

    /**
     * @var mixed
     */
    public $params;

    /**
     * All ORM events are from entities
     *
     * @param EntityInterface $entity
     * @param mixed $params additional parameters
     */
    public function __construct(EntityInterface $entity = null, $params = null)
    {
        $this->setEntity($entity);
        $this->params = $params;
    }

    /**
     * Sets the name of the class that triggers the event
     *
     * @param string $className
     * @return self|EventInterface|$this
     */
    public function setEntityName($className)
    {
        $this->entityName = $className;
        return $this;
    }

    /**
     * Gets the entity class name that triggers the event
     *
     * @return string
     */
    public function getEntityName()
    {
        if (null == $this->entityName) {
            $this->setEntityName(get_class($this->getEntity()));
        }
        return $this->entityName;
    }

    /**
     * Set event's action
     *
     * @param string $action
     * @return self|EventInterface|$this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Sets the entity object that triggers the event
     *
     * @param EntityInterface $entity
     * @return self|EventInterface|$this
     */
    public function setEntity(EntityInterface $entity = null)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Gets the entity object that triggers the event
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get the event name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->action;
    }
}