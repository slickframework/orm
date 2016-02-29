# get-entity-by-id.feature
  Feature: Get an entity by its id
    In order to retrieve an entity from its repository
    As a developer
    I want to have a repository::get(id) method to grab an existing object

  Scenario: Get an entity from repository
    Given I get a repository for "Domain\Person"
    When I get entity with id "2"
    Then I get entity with "name" equals "Ana"

  Scenario: An entity is unique in the entity map
    Given I get a repository for "Domain\Person"
    And I get entity with id "2"
    When I get entity with id "2" again
    Then entities should be the same