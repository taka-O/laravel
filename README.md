# README
laravel勉強用

# 環境
Nginx<br/>
PHP 8.2<br/>
Laravel 12.0<br/>
mysql 8.4<br/>

# Dockerセットアップ、および起動
docker compose up -d

# Docker laravel環境への接続
docker compose exec laravel bash

# test
php artisan test<br/>
php artisan test <ディレクトリパス> または <ファイルパス><br/>
php artisan test --filter <特定テスト名> <ディレクトリパス> または <ファイルパス>
