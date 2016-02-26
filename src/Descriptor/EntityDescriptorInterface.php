<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor;

/**
 * ORM Entity Descriptor Interface
 *
 * @package Slick\Orm\Descriptor
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface EntityDescriptorInterface
{

    /**
     * Gets entity table name
     *
     * @return string
     */
    public function getTableName();

    /**
     * Returns entity fields
     *
     * @return array
     */
    public function getFields();
}