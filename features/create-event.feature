# create-event.feature
  Feature: Create entity event
    In order to add business logic upon entity creation
    As a developer
    I want to register a listener class to a entity create event

  Scenario: Listener handler pre-save event
    Given I get a repository for "Domain\Person"
    And I get entity with id "2"
    And I register a listener to "before.update" event
    When I save it
    Then A "before.update" event was triggered

  Scenario: Listener handler pre-save event
    Given I get a repository for "Domain\Person"
    And I get entity with id "2"
    And I register a listener to "after.update" event
    When I save it
    Then A "after.update" event was triggered