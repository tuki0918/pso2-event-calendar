#!/bin/sh

# コンテナ内のホームディレクトリ
WORK_DIR="/var/www/html"

usage() {
    cat <<-EOF
    Usage: $0 {console|deploy|styling|test|setup}
EOF
    exit 1
}

case "$1" in
    # 任意のコマンド
    console)
        /usr/local/bin/docker container run --rm -it \
            -v ${PWD}:${WORK_DIR} \
            php:7.1-apache \
            php src/console.php $2
        ;;
    # デプロイ
    deploy)
        /usr/local/bin/docker container run --rm -it \
            -v ${PWD}:${WORK_DIR} \
            php:7.1-apache \
            php src/console.php calendar:fetch --deploy
        ;;
    # コーディング規約
    styling)
        /usr/local/bin/docker container run --rm -it \
            -v ${PWD}:${WORK_DIR} \
            php:7.1-apache \
            vendor/bin/php-cs-fixer fix -v
        ;;
    # テスト
    test)
        /usr/local/bin/docker container run --rm -it \
            -v ${PWD}:${WORK_DIR} \
            php:7.1-apache \
            vendor/bin/phpunit
        ;;
    # 設定
    init)
        /usr/local/bin/docker container run --rm \
            -v ${PWD}:/app \
            composer:1.5 install
        ;;
    update)
        /usr/local/bin/docker container run --rm \
            -v ${PWD}:/app \
            composer:1.5 update
        ;;
    *)
        usage
        ;;
esac
