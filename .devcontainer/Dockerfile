# VSCodeでサポートするOSの要件があるため、Ubuntuベースのイメージを使用する
# https://code.visualstudio.com/docs/remote/faq#_can-i-run-vs-code-server-on-older-linux-distributions
FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

# 各種パッケージのインストール
# php-dev: phpizeコマンド実行のために必要
RUN apt update && \
    apt-get install software-properties-common -y && \
    add-apt-repository ppa:ondrej/php -y && \
    apt update && \
    apt install php7.0 php7.0-dom php7.0-zip php7.0-dev php8.4-cli php8.4-xml php8.4-mbstring git unzip sudo curl xclip -y && \
    \
    php -r "copy('https://xdebug.org/files/xdebug-2.8.1.tgz', 'xdebug-2.8.1.tgz');" && \
    tar -xvzf xdebug-2.8.1.tgz && \
    cd xdebug-2.8.1 && \
    phpize && \
    ./configure && \
    make && \
    cp modules/xdebug.so /usr/lib/php/20151012/ && \
    echo "zend_extension=/usr/lib/php/20151012/xdebug.so" > /etc/php/7.0/cli/conf.d/99-xdebug.ini && \
    cd - && rm -rf xdebug-2.8.1 xdebug-2.8.1.tgz && \
    \
    apt-get autoremove --purge software-properties-common -y && \
    apt autoremove --purge php7.0-dev -y && \
    apt clean && rm -rf /var/lib/apt/lists/*

# PHPStan の実行には PHP 7.4 以上が必要なため php8.4 を併用する
# php8.4 のインストールにより update-alternatives の既定が 8.4 になるため、開発用の既定は最小サポートの 7.0 に戻す
RUN update-alternatives --set php /usr/bin/php7.0

# Composer `Latest 2.2.x LTS` をインストール
RUN php -r "copy('https://getcomposer.org/download/latest-2.2.x/composer.phar', '/usr/local/bin/composer');" && \
    chmod +x /usr/local/bin/composer

# Node.js (LTS) をインストール
# NodeSource の nodistro リポジトリを使用する
# https://github.com/nodesource/distributions
ARG NODE_MAJOR=24
RUN apt update && \
    apt install ca-certificates gnupg -y && \
    \
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_${NODE_MAJOR}.x nodistro main" > /etc/apt/sources.list.d/nodesource.list && \
    \
    apt update && \
    apt install nodejs -y && \
    node --version && npm --version && \
    \
    apt clean && rm -rf /var/lib/apt/lists/*

# GitHub CLI をインストール
# https://github.com/cli/cli/blob/trunk/docs/install_linux.md
RUN apt update && \
    curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | gpg --dearmor -o /etc/apt/keyrings/githubcli-archive-keyring.gpg && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" > /etc/apt/sources.list.d/github-cli.list && \
    \
    apt update && \
    apt install gh -y && \
    gh --version && \
    \
    apt clean && rm -rf /var/lib/apt/lists/*

# ubuntuユーザーを削除
RUN userdel -r ubuntu || true

ARG UID
ARG GID

# vscodeユーザーを追加
RUN groupadd -g ${GID} vscode && \
    useradd -m -u ${UID} -g ${GID} -s /bin/bash vscode

# vscodeユーザーはパスワード無しでsudoが実行できるようにする
RUN echo "vscode ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers.d/vscode

# ここからvscodeユーザーで操作
USER vscode
WORKDIR /home/vscode

# ~/.local/bin を PATH に追加
ENV PATH=/home/vscode/.local/bin:$PATH
