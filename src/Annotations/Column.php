<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Annotations;

use Slick\Database\Sql\Ddl\Column\Size;

/**
 * Column annotation
 *
 * @package Slick\Orm\Annotations
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Column extends OrmAnnotation
{

    /**
     * @var string
     */
    protected $name = 'column';

    /**
     * @var array
     */
    protected $parameters = [
        'type' => 'text',
        'length' => null,
        'size' => Size::TINY,
        'field' => null,
        'autoIncrement' => false,
        'primaryKey' => false,
        'index' => false
    ];
}