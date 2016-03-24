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
 * Entity Change Event Interface
 * 
 * @package Slick\Orm\Event
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EntityChangeEventInterface extends EventInterface
{

    /**
     * Gets the entity collection that triggers the event
     *
     * @return EntityCollectionInterface
     */
    public function getCollection();
}