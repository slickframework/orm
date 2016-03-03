# update-event.feature
  Feature: Update entity event
    In order to add business logic upon entity change
    As a developer
    I want to register a listener class to a entity update event

    Scenario: Listener handler pre-save event
      Given I create a person named 'Sonia'
      And I register a listener to "before.insert" event
      When I save it
      And I delete the entity
      Then A "before.insert" event was triggered

    Scenario: Listener handler post-save event
      Given I create a person named 'Sonia'
      And I register a listener to "after.insert" event
      When I save it
      And I delete the entity
      Then A "after.insert" event was triggered