# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-11-22

### Added

- PHP 8.4 / 8.5 / 8.6 に対応。

### Changed

- `Ulid::__construct()` の引数の型宣言を削除し、型を PHPDoc で表現するように変更（PHP 8.4 で非推奨となった暗黙的な null 許容を回避するため）。

## [1.0.0] - 2024-04-26

### Added

- 同一ミリ秒内で生成した場合でも単調増加するように変更。

[1.1.0]: https://github.com/yamaneyuta/php-ulid/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/yamaneyuta/php-ulid/compare/v0.2.1...v1.0.0