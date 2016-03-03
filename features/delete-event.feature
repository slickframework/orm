# delete-event.feature
  Feature: Update entity event
    In order to add business logic upon entity deletion
    As a developer
    I want to register a listener class to a entity delete event

    Scenario: Listener handler pre-delete event
      Given I create a person named 'Sonia'
      And I register a listener to "before.delete" event
      When I save it
      And I delete the entity
      Then A "before.delete" event was triggered

    Scenario: Listener handler post-delete event
      Given I create a person named 'Sonia'
      And I register a listener to "after.delete" event
      When I save it
      And I delete the entity
      Then A "after.delete" event was triggered