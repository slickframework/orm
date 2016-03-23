<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Event;
use Slick\Orm\Entity\EntityCollectionInterface;

/**
 * Entity Added: triggered when an entity is added to an entity collection
 * 
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityAdded extends AbstractEvent implements EventInterface
{
    
    const ACTION_ADD = 'entity.added';

    /**
     * @var string
     */
    protected $name = 'EntityAdded';

    /**
     * @var string
     */
    protected $action = self::ACTION_ADD;

    /**
     * Gets the entity collection that triggers the event
     * 
     * @return EntityCollectionInterface
     */
    public function getCollection()
    {
        return $this->params['collection'];
    }
}