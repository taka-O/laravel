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

# テストDBの作成
<ul>
<li>DBコンテナに接続</li>
docker compose exec db bash
<li>mysqlに接続</li>
mysql -u root -p<br>
パスワードは、secret（compose.ymlで定義しているもの）
<li>test databaseを作成</li>
create database test;<br>
</ul>

# test
php artisan test<br/>
php artisan test <ディレクトリパス> または <ファイルパス><br/>
php artisan test --filter <特定テスト名> <ディレクトリパス> または <ファイルパス>
