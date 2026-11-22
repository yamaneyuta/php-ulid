#!/usr/bin/env bash
#
# devcontainer 作成時 (postCreateCommand) に実行されるセットアップスクリプト
#
set -euo pipefail

# サードパーティ製ライブラリのインストール
install_vendor() {
    composer install
}

install_phpstan() {
    bash tools/install-phpstan.sh
}

# npm パッケージのインストール
install_global_npm_packages() {
    sudo npm install -g --ignore-scripts @earendil-works/pi-coding-agent
    echo "pi: $(pi --version 2>/dev/null || true)"
}

configure_git() {
    git config --global rebase.autoSquash true
    git config --global rebase.autoStash true
    git config --global rerere.enabled true
    git config --global fetch.prune true
}

main() {
    install_vendor
    install_phpstan
    install_global_npm_packages
    configure_git
}
main
