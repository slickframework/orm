<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Descriptor;


use Slick\Common\Utils\Text;

class EntityDescriptor implements EntityDescriptorInterface
{

    /**
     * @var string Entity class name
     */
    protected $entity;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * EntityDescriptor need an entity FQ class name.
     *
     * @param string $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * Gets entity table name
     *
     * @return string
     */
    public function getTableName()
    {
        if (null == $this->tableName) {
            $this->tableName = $this->parseTableName();
        }
        return $this->tableName;
    }

    /**
     * Returns entity fields
     *
     * @return array
     */
    public function getFields()
    {
        // TODO: Implement getFields() method.
    }

    /**
     * Parses the table name from the class name
     *
     * @return string
     */
    private function parseTableName()
    {
        $parts = explode('\\', $this->entity);
        $name = end($parts);
        $tableName = null;

        $words = explode('#', Text::camelCaseToSeparator($name, "#"));
        $last = array_pop($words);
        $last = Text::plural(strtolower($last));
        array_push($words, $last);
        foreach ($words as $word) {
            $tableName .= ucfirst($word);
        }
        return lcfirst($tableName);
    }
}