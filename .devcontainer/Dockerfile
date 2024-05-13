# VSCodeでサポートするOSの要件があるため、Ubuntuベースのイメージを使用する
# https://code.visualstudio.com/docs/remote/faq#_can-i-run-vs-code-server-on-older-linux-distributions
FROM ubuntu:24.04

# 各種パッケージのインストール
# php-dev: phpizeコマンド実行のために必要
RUN apt update && \
    apt-get install software-properties-common -y && \
    add-apt-repository ppa:ondrej/php -y && \
    apt update && \
    apt install php7.0 php7.0-dom php7.0-zip php7.0-dev git unzip -y && \
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

# Composer `Latest 2.2.x LTS` をインストール
RUN php -r "copy('https://getcomposer.org/download/latest-2.2.x/composer.phar', '/usr/local/bin/composer');" && \
    chmod +x /usr/local/bin/composer
