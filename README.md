# laravel-docker-template

## 環境構築

### Dockerビルド
1. git clone git@github.com:Estra-Coachtech/laravel-docker-template.git
2. DockerDesktopアプリを立ち上げる
3. docker-compose up -d --build

### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. アプリケーションキーの作成
```
php artisan key:generate
```
6. マイグレーションの実行
```
php artisan migrate
```
7. シーディングの実行
```
php artisan db:seed
```

## 使用技術(実行環境)

- PHP 8.1.33
- Laravel 8.83.8
- MySQL 8.0.26

## ER 図

## URL
- phpMyAdmin：http://localhost:8080/

- お問い合わせフォーム入力ページ:http://localhost/
- お問い合わせフォーム確認ページ:http://localhost/confirm
- サンクスページ :http://localhost/thanks
- 管理画面 :http://localhost/admin
- ユーザ登録ページ :http://localhost/register
- ログインページ :http://localhost/login
