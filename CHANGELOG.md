# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0]
### Added
- Support from PHP 8.0

### Removed
- Removed (temporary) paysera/lib-php-cs-fixer-config
  
## [1.1.0]
### Added
- `NormalizationContext::markNullValuesForRemoval` method to be called in normalizers.
If this is called, elements with `null` values will be removed from currently normalized object.

## [1.0.0]
### Changed
- `null` values are kept by `DataFilter` and will be available in resulted normalized data.

## 0.1.1
### Added
- Support for normalization groups:
    - `GroupedNormalizerRegistryProvider`, which allows to register normalizers for different groups;
    - `NormalizerRegistryProviderInterface`;
    - `NormalizationContext` and `DenormalizationContext` can be configured with any `normalizationGroup`.

### Changed
- `CoreNormalizer` and `CoreDenormalizer` takes `NormalizerRegistryProviderInterface` instead of
    `NormalizerRegistryInterface` as a first constructor argument.

### Removed
- `Paysera\Component\Normalization\NormalizerRegistry` class.
    Use `Paysera\Component\Normalization\Registry\GroupedNormalizerRegistryProvider` to register normalizers
    and get appropriate registries for concrete normalization groups.
    Always type-hint `Paysera\Component\Normalization\NormalizerRegistryInterface`;
- Unused constant `Paysera\Component\Normalization\NormalizerRegistryInterface::DENORMALIZER_TYPE_ARRAY`.


[1.0.0]: https://github.com/paysera/lib-normalization/compare/0.1.3...1.0.0
[1.1.0]: https://github.com/paysera/lib-normalization/compare/1.0.0...1.1.0
