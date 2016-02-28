# create-entity-feature.feature
  Feature: Create an entity object and persist it
    In order to create an entity object and persist it
    As a developer
    I want to instantiate the object a and persist it using save() method

  Scenario: Create Michel person (entity)
    Given I create a person named 'Joe'
    When I save it
    Then I should see in database "people" table a row where "name" equals "Joe"