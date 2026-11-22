#!/usr/bin/env bash
set -euo pipefail

# PHPStan は実行に PHP 7.4 以上が必要なため composer ではなく PHAR で導入する
PHPSTAN_VERSION='2.3.7'

cd "$(dirname "$0")"
curl -fsSL -o phpstan.phar "https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar"
php8.5 phpstan.phar --version
