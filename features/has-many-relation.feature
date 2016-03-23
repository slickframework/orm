# has-many-relation.feature
  Feature: One entity "has many" entities
    In order to set a "has many" (one-to-many) relation between two entities
    As a developer
    I want to set an annotation "HasMany" in a property of an entity marking
      it as a relation property that when called will return the a collection
      of entities that "belongs to" the current entity

  Scenario: Retrieve a collection of posts
    Given I get a repository for "Domain\Person"
    And I get entity with id "1"
    When I retrieve entity "posts" property
    Then property should be an instance of "Slick\Orm\Entity\EntityCollection"

  Scenario:  Retrieve an empty collection of posts
    Given I get a repository for "Domain\Person"
    And I get entity with id "2"
    When I retrieve entity "posts" property
    Then property should be an instance of "Slick\Orm\Entity\EntityCollection"
    And entity collection should be empty

  Scenario: Add a post to a person
    Given I create a post with:
      | title | body |
      | post1 | test |
    And I get a repository for "Domain\Person"
    And I get entity with id "2"
    When I add the post to entity
    And I retrieve entity "posts" property
    Then entity collection should not be empty
    And I should see in database "posts" table a row where "title" equals "post1"
    And I delete the post