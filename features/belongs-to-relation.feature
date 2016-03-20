# belongs-to-relation.feature
  Feature: One entity "belongs to" another entity
    In order to set a "belongs to" relation between two entities
    As a developer
    I want to set an annotation "BelongsTo" in a property of an entity marking
      it as a relation property that when called will return the other entity
      object.

  NOTES:
    A "belongs to" relation between two entities is a representation of a
    many-to-one relationship.

    Properties:
    - className: FQ entity class name (Mandatory)
    - foreignKey: singular name of table with "_id" suffix. Ex.: person_id
    - lazyLoaded: it defaults to false. When true entity will be loaded with its parent
    - dependent: defaults to true. when deleting the parent entity it child will be deleted
                 False will prevent parent entity to be deleted.

  Scenario: Retrieving the Person when loading a Profile
    Given I get a repository for "Domain\Profile"
    And I get entity with id "1"
    When I retrieve entity "person" property
    Then property should be an instance of "Domain\Person"

  Scenario: Retrieving an orphan entity
    Given I get a repository for "Domain\Profile"
    And I get entity with id "2"
    When I retrieve entity "person" property
    Then property should be null

  Scenario: Save entity with related entity set
    Given I get a repository for "domain\Person"
    And I get entity with id "2"
    When I create a profile with:
      | email           | person|
      | ana@example.com | 2     |
    And I save it
    When I retrieve entity "person" property
    Then property should be an instance of "Domain\Person"
    And I delete the entity

