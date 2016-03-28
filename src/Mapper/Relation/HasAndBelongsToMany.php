<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Orm\Mapper\Relation;
use Slick\Common\Utils\Text;

/**
 * HasAndBelongsToMany relation
 * 
 * @package Slick\Orm\Mapper\Relation
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HasAndBelongsToMany extends HasMany
{

    /**
     * @readwrite
     * @var string
     */
    protected $relatedForeignKey;

    /**
     * @readwrite
     * @var string
     */
    protected $relationTable;

    /**
     * Gets the related foreign key
     * 
     * @return string
     */
    public function getRelatedForeignKey()
    {
        return $this->relatedForeignKey;
    }

    /**
     * Gets the related table
     * 
     * @return string
     */
    public function getRelationTable()
    {
        if (is_null($this->relationTable)) {
            $parentTable = $this->getParentTableName();
            $table = $this->getEntityDescriptor()->getTableName();
            $names = [$parentTable, $table];
            asort($names);
            $first = array_shift($names);
            $tableName = Text::camelCaseToSeparator($first, '#');
            $parts = explode('#', $tableName);
            $lastName = array_pop($parts);
            $lastName = Text::singular(strtolower($lastName));
            array_push($parts, ucfirst($lastName));
            $tableName = lcfirst(implode('', $parts));
            array_unshift($names, $tableName);
            $this->relationTable = implode('_', $names);

        }
        return $this->relationTable;
    }
        
}