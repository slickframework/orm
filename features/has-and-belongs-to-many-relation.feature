# has-and-belongs-to-many.feature
  Feature: One entity "has and belongs to many" entities
    In order to set a "has and belongs to many" (many-to-many) relation between two entities
    As a developer
    I want to set an annotation "HasAndBelongsToMany" in a property of an entity marking
    it as a relation property that when called will return the a collection
    of entities that "belongs to" the current entity

  Scenario: Retrieve a collection of posts
    Given I get a repository for "Domain\Tag"
    And I get entity with id "1"
    When I retrieve entity "posts" property
    Then property should be an instance of "Slick\Orm\Entity\EntityCollection"