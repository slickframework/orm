# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v2.3.0] - 2025-03-15
### Adds
- Possibility to add a regular expression to filter out existing schema tables.

## [v2.2.0] - 2024-08-29
### Fixed
- `slick/webstack` changes the configuration interface to use `ConfigurationInterface`, resulting in
  an initialization error.

## [v2.1.3] - 2024-08-13
### Added
- Event handling when (en|dis)abling the module.

## [v2.0.3] - 2024-07-14
### Fixed
- Updates the `slick/module-api` witch adds a DI container to be used on console setup.

## [v2.0.2] - 2024-07-10
### Fixed
- Updates the `slick/module-api` witch has a fix for array functions file autoload

## [v2.0.0] - 2024-07-10
### Added
- Doctrine DBAL driver middleware to log database operations with duration 
- `EntityCollection`, `EntityManagerFactory` to manages ORM and DBAL access to databases
- `doctrine/orm` and `doctrine/migrations` console commands
- `doctrine/orm` and `doctrine/migrations` integration module on `slick/webstack`
- Code quality tools: `phpstan` `phpmd`

### Changed
- Converted to a slick v2.* module

### Removed
- PHP <= 8.1 support
- All API from slick <= v1.2

## [v1.2.0] - 2016-03-31
### Added
- Completely remake for Slick v1.2.0.
- First released stand alone ORM package for Slick framework.

[Unreleased]: https://github.com/slickframework/orm/compare/v2.3.0...HEAD
[v2.3.0]: https://github.com/slickframework/orm/compare/v2.2.0...v2.3.0
[v2.2.0]: https://github.com/slickframework/orm/compare/v2.1.0...v2.2.0
[v2.1.0]: https://github.com/slickframework/orm/compare/v2.0.3...v2.1.0
[v2.0.3]: https://github.com/slickframework/orm/compare/v2.0.2...v2.0.3
[v2.0.2]: https://github.com/slickframework/orm/compare/v2.0.0...v2.0.2
[v2.0.0]: https://github.com/slickframework/orm/compare/v1.2.0...v2.0.0
[v1.2.0]: https://github.com/slickframework/orm/compare/724593...v1.2.0