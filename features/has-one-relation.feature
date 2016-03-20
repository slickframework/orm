# has-one-relation.feature
  Feature: On entity "has one" other entity
    In order to set a "has one" (one-to-one) relation between two entities
    As a developer
    I want to set an annotation "BelongsTo" in a property of an entity marking
      it as a relation property that when called will return the other entity
      object.

  Scenario: Retrieves a person profile
    Given I get a repository for "Domain\Person"
    And I get entity with id "1"
    When I retrieve entity "profile" property
    Then property should be an instance of "Domain\Profile"
