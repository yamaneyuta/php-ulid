# Pi 補足

## AIツール名およびモデル表示名の取得方法

- AGENTS.md に記載の `<AI tool>` は `Pi` を使用する。
- AGENTS.md に記載の `<model>` にはモデル表示名を使うが、環境変数 `PI_MODEL` はモデル ID（例: `OpenDesign/mimo-v2.6-pro`）しか持たない。
- 表示名はエージェントディレクトリ（`PI_CODING_AGENT_DIR`、既定 `~/.pi/agent`）の `models.json` で、対象モデル定義の `name` フィールドを参照して確認する（例: `OpenDesign/mimo-v2.6-pro` → `MiMo-V2.6-Pro`）。
