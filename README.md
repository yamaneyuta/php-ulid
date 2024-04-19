


# php-ulid
<img src="https://img.shields.io/badge/PHP-ccc.svg?logo=php&style=flat"> ![License](https://img.shields.io/github/license/yamaneyuta/php-ulid) ![Test](https://github.com/yamaneyuta/php-ulid/actions/workflows/test.yml/badge.svg?branch=main) ![Lint](https://github.com/yamaneyuta/php-ulid/actions/workflows/lint.yml/badge.svg?branch=main) ![Tested on PHP 7.1 to 8.3](https://img.shields.io/badge/tested%20on-PHP%207.0%20|%207.1%20|%207.2%20|%207.3%20|%207.4%20|%208.0%20|%208.1%20|%208.2%20|%208.3-brightgreen.svg?maxAge=2419200)

PHPでULID(Universally Unique Lexicographically Sortable Identifier)を生成するためのライブラリです。

UUIDと同じフォーマットでの出力が可能なため、システムへの影響を最小限に、ミリ秒単位でソート可能なIDに置き換えることができます。

また、16進数のフォーマットで出力することも可能なため、128ビットの数値として扱うことも容易です。

## 使い方

### インストール

```bash
composer require yamaneyuta/ulid
```

### コード

新しくULIDを生成
```php
use yamaneyuta\Ulid;

echo (string)new Ulid(); // 01HVNE93FHMTQ38NSJ81M03H1Y
```

または

```php
use function yamaneyuta\ulid;

echo ulid(); // 01HVTDK9CSD1F58S8YGK6M610X
```

他のフォーマットで出力
```php
use yamaneyuta\Ulid;

$ulid = new Ulid();

// UUIDのフォーマットで出力
echo $ulid->toUuid(); // 018eeae8-64fd-06c9-19b6-3138fd763df8

// 16進数のフォーマットで出力
echo $ulid->toHex();  // 018eeae864fd06c919b63138fd763df8
```
