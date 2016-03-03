<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;

use League\Event\EventInterface as LeagueEventInterface;
use Slick\Orm\EntityInterface;

/**
 * Event Interface: Defines an ORM event
 *
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EventInterface extends LeagueEventInterface
{

    /**
     * Sets the name of the class that triggers the event
     *
     * @param string $className
     * @return self|EventInterface|$this
     */
    public function setEntityName($className);

    /**
     * Gets the entity class name that triggers the event
     *
     * @return string
     */
    public function getEntityName();

    /**
     * Set event's action
     *
     * @param string $action
     * @return self|EventInterface|$this
     */
    public function setAction($action);

    /**
     * Sets the entity object that triggers the event
     *
     * @param EntityInterface $entity
     * @return self|EventInterface|$this
     */
    public function setEntity(EntityInterface $entity);

    /**
     * Gets the entity object that triggers the event
     *
     * @return EntityInterface
     */
    public function getEntity();

}