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
use Behat\Gherkin\Node\TableNode;
use Domain\Person;
use Domain\Post;
use Domain\Profile;
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
     * @var Entity\EntityCollection
     */
    protected $collection;

    /**
     * @var mixed
     */
    protected $selectedValue;

    /**
     * @var Post
     */
    protected $post;


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

    /**
     * @When /^I try to find all entities$/
     */
    public function iTryToFindAllEntities()
    {
        $this->collection = $this->repository->find()->all();
    }

    /**
     * @Then /^I should get an entity collection$/
     */
    public function iShouldGetAnEntityCollection()
    {
        Assert::assertInstanceOf(Entity\EntityCollection::class, $this->collection);
    }

    /**
     * @Then /^it should be the same as entity in collection at position "([^"]*)"$/
     */
    public function itShouldBeTheSameAsEntityInCollectionAtPosition($offset)
    {
        Assert::assertSame($this->collection[$offset], $this->entity);
    }

    /**
     * @When /^I try to find first match$/
     */
    public function iTryToFindFirstMatch()
    {
        $this->lastEntity = $this->entity;
        $this->entity = $this->repository->find()->first();
    }

    /**
     * @Then /^property should be an instance of "([^"]*)"$/
     * @param string $expected
     */
    public function propertyShouldBeAnInstanceOfDomainPerson($expected)
    {
        Assert::assertInstanceOf($expected, $this->selectedValue);
    }

    /**
     * @param $property
     * @When /^I retrieve entity "([^"]*)" property$/
     */
    public function iRetrieveEntityProperty($property)
    {
        $this->selectedValue = $this->entity->$property;
    }

    /**
     * @Then /^property should be null$/
     */
    public function propertyShouldBeNull()
    {
        Assert::assertNull($this->selectedValue);
    }

    /**
     * @When /^I create a profile with:$/
     */
    public function iCreateAProfileWith(TableNode $table)
    {
        $this->lastEntity = $this->entity;
        $hash = $table->getHash();
        $this->entity = new Profile($hash[0]);
    }

    /**
     * @Given /^entity collection should be empty$/
     */
    public function entityCollectionShouldBeEmpty()
    {
        Assert::assertEmpty($this->selectedValue);
    }

    /**
     * @Given /^I create a post with:$/
     */
    public function iCreateAPostWith(TableNode $table)
    {
        $hash = $table->getHash();
        $this->post = new Post($hash[0]);
        $this->post->save();
    }

    /**
     * @When /^I add the post to entity$/
     */
    public function iAddThePostToEntity()
    {
        $this->entity->posts->add($this->post);
    }

    /**
     * @Then /^entity collection should not be empty$/
     */
    public function entityCollectionShouldNotBeEmpty()
    {
        Assert::assertNotEmpty($this->selectedValue);
    }

    /**
     * @Given /^I delete the post$/
     */
    public function iDeleteThePost()
    {
        $this->post->delete();
    }

    /**
     * @Then /^collection should not have a person named "([^"]*)"$/
     */
    public function collectionShouldNotHaveAPersonManed($name)
    {
        $found = false;
        foreach ($this->collection as $person) {
            if ($name == $person->name) {
                $found = true;
                break;
            }
        }
        Assert::assertFalse($found);
    }
}