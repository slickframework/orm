<?php

/**
 * This file is part of slick/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Orm;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Domain\Person;
use PHPUnit_Framework_Assert as Assert;
use Slick\Database\Adapter;
use Slick\Database\Adapter\SqliteAdapter;
use Slick\Database\Sql;
use Slick\Orm\Entity;
use Slick\Orm\Orm;

/**
 * Step definitions for slick/orm package
 *
 * @package Orm
 * @behatContext
 */
class OrmContext extends \AbstractContext implements
    Context, SnippetAcceptingContext
{

    public function __construct()
    {
        $this->getOrm();
    }

    /**
     * @var SqliteAdapter
     */
    protected $adapter;

    /**
     * @var Orm
     */
    protected $orm;

    /**
     * @var Person|Entity
     */
    protected $entity;

    /**
     * Create a person with provided name
     *
     * @Given /^I create a person named \'([^\']*)\'$/
     * @param string $name
     */
    public function iCreateAPersonNamed($name)
    {
        $this->entity = new Person(['name' => $name]);
    }

    /**
     * Run save method on current entity
     *
     * @When /^I save it$/
     */
    public function iSaveIt()
    {
        $this->entity->save();
    }

    /**
     * Run query where field equals provided pattern
     *
     * @Then /^I should see in database "([^"]*)" table a row where "([^"]*)" equals "([^"]*)"$/
     *
     * @param string $table
     * @param string $field
     * @param string $pattern
     */
    public function iShouldSeeInDatabaseTableFieldEquals($table, $field, $pattern)
    {
        $found = Sql::createSql($this->getAdapter())
            ->select($table)
            ->where(["{$table}.{$field} = ?" => $pattern])
            ->count();
        Assert::assertTrue($found > 0);
    }

    /**
     * @return SqliteAdapter
     */
    protected function getAdapter()
    {
        if (null == $this->adapter) {
            $this->adapter = Adapter::create(
                [
                    'driver' => Adapter::DRIVER_SQLITE,
                    'options' => [
                        'file' => dirname(dirname(dirname(__DIR__))).
                            '/tests/test.db'
                    ]
                ]
            );
        }
        return $this->adapter;
    }

    /**
     * @return Orm
     */
    protected function getOrm()
    {
        if (null == $this->orm) {
            Orm::getInstance()->setDefaultAdapter($this->getAdapter());
            $this->orm = Orm::getInstance();
        }
        return $this->orm;
    }
}