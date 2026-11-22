#!/usr/bin/env bash
#
# devcontainer 作成時 (postCreateCommand) に実行されるセットアップスクリプト
#
set -euo pipefail

# サードパーティ製ライブラリのインストール
install_vendor() {
    composer install
}

# npm パッケージのインストール
install_global_npm_packages() {
    sudo npm install -g --ignore-scripts @earendil-works/pi-coding-agent
    echo "pi: $(pi --version 2>/dev/null || true)"
}

main() {
    install_vendor
    install_global_npm_packages
}
main
