# Release Notes for ElasticSearch

## 5.0.0-beta.5

### Fixed
- Fixed an issue where new elements being saved for the first time would not be indexed in propagated sites' Elasticsearch indexes.

## 5.0.0-beta.4

### Added
- Moved Entry status filtering to the index config, allowing per-index control over which statuses are included.

## 5.0.0-beta.3

### Fixed
- Fixed an issue where the plugin would match the wrong index because of a typo in the `ElementIndexCriteriaFactory` `element` property.

## 5.0.0-beta.2

### Added
- Added code-first `fields` configuration array for strict, programmatic control over Elasticsearch mappings.
- Added `*` wildcard support in the `fields` array to easily index all custom fields.
- Added deep relational mapping capabilities. You can now explicitly define which custom fields should be indexed from related elements (e.g., pulling `bio` and `avatar` from an Author entries field).

### Fixed
- Fixed an exception where Matrix blocks and other headless elements threw an error during indexing due to missing `section` properties.

## 5.0.0-beta.1
- Initial release
