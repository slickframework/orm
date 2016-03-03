<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper;

use Slick\Database\Sql\Insert;
use Slick\Orm\EntityInterface;
use Slick\Orm\Event\Delete;
use Slick\Orm\Event\Save;
use Slick\Orm\Orm;

/**
 * ORM entity event triggers methods
 *
 * @package Slick\Orm\Mapper
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EventTriggers
{

    /**
     * Triggers the before save event
     *
     * @param \Slick\Database\Sql\SqlInterface $query
     * @param EntityInterface $entity
     * @param array $data
     *
     * @return Save
     */
    protected function triggerBeforeSave(
        $query, EntityInterface $entity, array $data
    ) {
        $save = $query instanceof Insert
            ? (new Save($entity, $data))
                ->setAction(Save::ACTION_BEFORE_INSERT)
            : (new Save($entity, $data))
                ->setAction(Save::ACTION_BEFORE_UPDATE);

        /** @var Save $save */
        return Orm::getEmitter($this->getEntityClassName())->emit($save);
    }

    /**
     * Triggers the after save event
     *
     * @param Save $saveEvent
     * @param EntityInterface $entity
     *
     * @return \League\Event\EventInterface|Save
     */
    protected function triggerAfterSave(
        Save $saveEvent, EntityInterface $entity
    ) {
        $afterSave = clone $saveEvent;
        $action = $afterSave->getName() == Save::ACTION_BEFORE_INSERT
            ? Save::ACTION_AFTER_INSERT
            : Save::ACTION_AFTER_UPDATE;

        $afterSave->setEntity($entity)->setAction($action);
        return Orm::getEmitter($this->getEntityClassName())
            ->emit($afterSave);
    }

    /**
     * Triggers the before delete event
     *
     * @param EntityInterface $entity
     *
     * @return \League\Event\EventInterface|Delete
     */
    protected function triggerBeforeDelete(EntityInterface $entity)
    {
        $event = new Delete($entity);
        $event->setAction(Delete::ACTION_BEFORE_DELETE);
        return Orm::getEmitter($this->getEntityClassName())
            ->emit($event);
    }

    /**
     * Triggers the after delete event
     *
     * @param EntityInterface $entity
     *
     * @return \League\Event\EventInterface|Delete
     */
    protected function triggerAfterDelete(EntityInterface $entity)
    {
        $event = new Delete($entity);
        $event->setAction(Delete::ACTION_AFTER_DELETE);
        return Orm::getEmitter($this->getEntityClassName())
            ->emit($event);
    }

    /**
     * @return string
     */
    abstract public function getEntityClassName();
}