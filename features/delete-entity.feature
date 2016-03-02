# delete-entity.feature
  Feature: Delete an entity
    In order to completely delete an entity from its storage
    As a developer
    I want to call have a Entity::delete() method

  Notes:
    - Calling delete will remove the entity from the identity map
      and database/storage associated with it.

  Scenario: Delete an entity
    Given I create a person named 'Julia'
    And I save it
    And I should see in database "people" table a row where "name" equals "Julia"
    When I delete the entity
    Then I should not see in database "people" table a row where "name" equals "Julia"