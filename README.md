


# php-ulid <img src="https://img.shields.io/badge/PHP-ccc.svg?logo=php&style=flat"> ![License](https://img.shields.io/github/license/yamaneyuta/php-ulid) ![Test](https://github.com/yamaneyuta/php-ulid/actions/workflows/test.yml/badge.svg?branch=main)

PHPでULID(Universally Unique Lexicographically Sortable Identifier)を生成するためのライブラリです。

UUIDと同じフォーマットでの出力が可能なため、システムへの影響を最小限に、ミリ秒単位でソート可能なIDに置き換えることができます。

また、16進数のフォーマットで出力することも可能なため、128ビットの数値として扱うことも容易です。

## 使い方
### コード

新しくULIDを生成
```php
use yutayamane\Ulid;

echo (string)new Ulid(); // 01HVNE93FHMTQ38NSJ81M03H1Y
```

他のフォーマットで出力
```php
use yutayamane\Ulid;

$ulid = new Ulid();

// UUIDのフォーマットで出力
echo $ulid->toUuid(); // 018eeae8-64fd-06c9-19b6-3138fd763df8

// 16進数のフォーマットで出力
echo $ulid->toHex();  // 018eeae864fd06c919b63138fd763df8
```
