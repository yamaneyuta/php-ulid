# リリース手順

Packagist は GitHub の webhook（`https://packagist.org/api/github`、`push` イベント）で main への push とタグの push を自動反映する。
そのためリリース時に必要な作業はタグと GitHub Release の作成だけで、`composer.json` などのバージョン表記の更新は不要。

## 1. リリース内容の確認

前回のタグとの差分を確認し、SemVer でバージョンを決める。

```bash
# 変更されたコミット
git log --oneline "$(git describe --tags --abbrev=0)..HEAD"

# 変更されたファイル
git diff --stat "$(git describe --tags --abbrev=0)..HEAD"
```

| 変更内容 | バージョン |
| --- | --- |
| 後方互換性のない変更（`composer.json` の `require` の php 制約を狭める、公開 API の削除・改名・引数追加など） | major |
| 後方互換性を保った機能追加・非推奨化 | minor |
| 後方互換性を保った修正のみ | patch |

## 2. CHANGELOG.md の更新

変更内容を `CHANGELOG.md` の先頭に追記する。見出しは `## [x.y.z] - 公開日` とし、カテゴリは Keep a Changelog の Added / Changed / Deprecated / Removed / Fixed / Security を使う。

```markdown
## [1.1.0] - 2026-11-22

### Added

- PHP 8.4 / 8.5 / 8.6 に対応しました。
```

末尾の比較用リンクも新しいバージョン向けに追加する。

## 3. 事前確認

- main の CI（lint / PHPStan / PHPUnit）が成功していること

```bash
gh run list --branch main --limit 1
```

- `composer.json` が有効であること

```bash
composer validate --strict
```

## 4. タグと GitHub Release の作成

```bash
git tag -a v1.1.0 -m 'v1.1.0'
git push origin main
git push origin v1.1.0
gh release create v1.1.0 --notes-file <(awk '/^## \[1\.1\.0\]/{f=1;next} /^## \[/{f=0} f' CHANGELOG.md)
```

タグは main の push より後に push する。タグが参照するコミットがリモートに存在しないと Packagist が取得できない。

タグ名は `.husky/pre-push` が検証し、セマンティックバージョン（`vX.Y.Z`）以外のタグは push が拒否される。

push はユーザーの明示的な承認を得てから実行する（AGENTS.md 参照）。

`--generate-notes` は PR のマージを前提に GitHub が本文を生成するため、main への直接コミットが中心の本リポジトリでは Full Changelog のリンクのみになる。そのため `CHANGELOG.md` の内容を `--notes-file` で渡す。

## 5. Packagist への反映確認

push から数十秒〜数分で反映される。反映後もしばらく古い参照が返るのは CDN のキャッシュのためで、`Cache-Control: no-cache` は効かないので URL にクエリを付ける。

```bash
curl -s "https://packagist.org/packages/yamaneyuta/ulid.json?cb=$(date +%s)" \
  | python3 -c 'import json,sys; [print(k, v["source"]["reference"]) for k, v in json.load(sys.stdin)["package"]["versions"].items()]'
```

タグが反映されない場合は webhook の配送状況を確認する。

```bash
gh api repos/yamaneyuta/php-ulid/hooks
gh api repos/yamaneyuta/php-ulid/hooks/<hook id>/deliveries
```

## 補足

- `dev-main` は main の push で自動更新される擬似バージョン。タグ付けやファイル更新は不要。
- 公開済みのタグは書き換えない。Packagist 側に古いコミットがキャッシュされる。
- `composer.json` に `version` フィールドを設定しない（Packagist はタグからバージョンを決定する）。
- リリースノートは Packagist のバージョン解決に影響しない（Packagist はタグと `composer.json` のみを参照する）。
- `CHANGELOG.md` は利用者向けのため、`.gitattributes` の `export-ignore` には追加しない。
- README のバッジは動的生成されるため更新不要。