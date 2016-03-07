# find-entities.feature
  Feature: Use repository to find/retrieve entities
    In order to retrieve/find entities is its repository
    As a developer
    I want to have a QueryObject that i can define search criteria using it in the repository

  Note:
    This QueryObject is similar to the Select object from Slick/Database package
    that already has methods for where clause, joins, limits and fields. It will return
    a collection of matched entities or empty collection if no match was found.

    A first() method will return only the 1st entity with in a matched collection or null
    if collection is empty.

  Scenario: Retrieving all entities in a repository
    Given I get a repository for "Domain\Person"
    When I try to find all entities
    Then I should get an entity collection

  Scenario: Retrieving a single entity does not run query
    Given I get a repository for "Domain\Person"
    And I try to find all entities
    When I get entity with id "2"
    Then it should be the same as entity in collection at position "1"

  Scenario: Retrieving first match
    Given I get a repository for "Domain\Person"
    When I try to find first match
    And  I get entity with id "1"
    Then entities should be the same
