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