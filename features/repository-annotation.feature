# repository-annotation.feature
  Feature: Repository annotation
    In order to set a custom entity repository
    As a developer
    I want to add a @repository annotation that specifies the class to instantiate

    Constraints:
      - Class must exists
      - class must implement the RepositoryInterface

  Scenario: Set a custom repository to an entity
    Given I get a repository for "Domain\Post"
    Then repository is instance of "Domain\Repository\PostsRepository"
