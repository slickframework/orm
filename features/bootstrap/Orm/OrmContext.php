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
use Slick\Orm\EntityInterface;
use Slick\Orm\Orm;
use Slick\Orm\Repository\EntityRepository;

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
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var EntityInterface
     */
    protected $lastEntity;

    /**
     * @var Listener
     */
    protected $listener;


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
     * Run query where field equals provided pattern and check its empty
     *
     * @Then /^I should not see in database "([^"]*)" table a row where "([^"]*)" equals "([^"]*)"$/
     *
     * @param string $table
     * @param string $field
     * @param string $pattern
     */
    public function iShouldNotSeeInDatabaseTableFieldEquals($table, $field, $pattern)
    {
        $found = Sql::createSql($this->getAdapter())
            ->select($table)
            ->where(["{$table}.{$field} = ?" => $pattern])
            ->count();
        Assert::assertTrue($found == 0);
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

    /**
     * Create repository for a provided class name
     *
     * @Given /^I get a repository for "([^"]*)"$/
     *
     * @param string $className
     */
    public function iGetARepositoryForDomainPerson($className)
    {
        $this->repository = $this->getOrm()
            ->getRepositoryFor($className);
    }

    /**
     * Gets the entity with
     *
     * @When /^I get entity with id "([^"]*)"$/
     *
     * @param string $entityId
     */
    public function iGetEntityWithId($entityId)
    {
        $this->lastEntity = $this->entity;
        $this->entity = $this->repository->get($entityId);
    }

    /**
     * Check if entity value matched
     *
     * @Then /^I get entity with "([^"]*)" equals "([^"]*)"$/
     *
     * @param string $field
     * @param string $value
     */
    public function iGetEntityWithEquals($field, $value)
    {
        Assert::assertEquals($value, $this->entity->{$field});
    }

    /**
     * Get same entity again
     *
     * @When /^I get entity with id "([^"]*)" again$/
     *
     * @param $entityId
     */
    public function iGetEntityWithIdAgain($entityId)
    {
        $this->iGetEntityWithId($entityId);
    }

    /**
     * Check that the 2 last entities are the same
     *
     * @Then /^entities should be the same$/
     */
    public function entityShouldBeTheSame()
    {
        Assert::assertSame($this->lastEntity, $this->entity);
    }

    /**
     * @Given /^entity ID should be assigned as an integer$/
     */
    public function entityIDShouldBeAssignedAsAnInteger()
    {
        Assert::assertTrue(is_integer($this->entity->getId()));
    }

    /**
     * @When /^I delete the entity$/
     */
    public function iDeleteTheEntity()
    {
        $this->entity->delete();
    }

    /**
     * @Given /^I register a listener to "([^"]*)" event$/
     */
    public function iRegisterAListenerToEvent($event)
    {
        Orm::addListener(Person::class, $event, $this->getListener());
    }

    /**
     * @Then /^A "([^"]*)" event was triggered$/
     */
    public function aEventWasTriggered($event)
    {
        Assert::assertEquals($event, $this->getListener()->event->getName());
    }

    /**
     * @return Listener
     */
    protected function getListener()
    {
        if (null == $this->listener) {
            $this->listener = new Listener();
        }
        return $this->listener;
    }
}