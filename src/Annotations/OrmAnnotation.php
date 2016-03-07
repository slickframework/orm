<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Annotations;

use Slick\Common\Annotation\Basic;

/**
 * Orm Annotation
 *
 * @package Slick\Orm\Annotations
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class OrmAnnotation extends Basic
{

    /**
     * Get all parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}