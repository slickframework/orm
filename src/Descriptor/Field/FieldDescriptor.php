<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor\Field;

use Slick\Common\Base;
use Slick\Database\Sql\Ddl\Column\Size;

/**
 * Table Field Descriptor
 *
 * @package Slick\Orm\Descriptor\Field
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property string $name
 * @property string $type
 * @property int    $length
 * @property string $size
 *
 * @method bool isPrimaryKey()
 * @method bool isIndex()
 * @method bool isAutoIncrement()
 */
class FieldDescriptor extends Base
{
    /**
     * @readwrite
     * @var bool
     */
    protected $primaryKey = false;

    /**
     * @readwrite
     * @var string
     */
    protected $name;

    /**
     * @readwrite
     * @var string
     */
    protected $type = 'text';

    /**
     * @readwrite
     * @var bool
     */
    protected $index;

    /**
     * @readwrite
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * @readwrite
     * @var string
     */
    protected $field;

    /**
     * @readwrite
     * @var integer
     */
    protected $length;

    /**
     * @readwrite
     * @var string
     */
    protected $size = Size::TINY;

    /**
     * @write
     * @var string
     */
    protected $raw;

    /**
     * Gets field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the field name
     *
     * @return string
     */
    public function getField()
    {
        $value = $this->name;
        if (null != $this->field) {
            $value = $this->field;
        }
        return $value;
    }

    /**
     * Sets field name
     *
     * @param string $name
     * @return $this|self|FieldDescriptor
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}