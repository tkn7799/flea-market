# 模擬案件フリマアプリ

## 環境構築

### Docker ビルド

1. git clone https://github.com/tkn7799/flea-market.git
2. cd flea-market/
3. DockerDesktop アプリを立ち上げる
4. docker-compose up -d --build

### Laravel 環境構築

1. docker-compose exec php bash
2. cd flea-market/
3. composer install
4. PHP コンテナ上で実行

```
cp .env.example .env
exit
```

5. .env に以下の環境変数を追加
   STRIPE_KEY と STRIPE_SECRET は新規追加

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_FROM_ADDRESS=no-reply@example.com

STRIPE_KEY={stripeのAPI公開可能キー}
STRIPE_SECRET={stripeのAPIシークレットキー}


```

5. アプリケーションキーの作成

PHP コンテナ上

```
php artisan key:generate
```

6. マイグレーションの実行

```
php artisan migrate
```

7. シンボリックリンク作成

```
php artisan storage:link
```

8. シーディングの実行

```
php artisan db:seed
```

### テスト用ユーザー情報

1. 出品者 A

```
メールアドレス：sellerA@example.com
パスワード：password123
出品した商品：腕時計、革靴、タンブラー
※初回ログイン時は認証メールを再送してください
```

2. 出品者 B

```
メールアドレス：sellerB@example.com
パスワード：password123
出品した商品：HDD、ノートPC、ショルダーバッグ、メイクセット
※初回ログイン時は認証メールを再送してください
```

3. 出品者 C

```
メールアドレス：sellerC@example.com
パスワード：password123
出品した商品：玉ねぎ3束、マイク、コーヒーミル
※初回ログイン時は認証メールを再送してください
```

4. テストユーザー

```
メールアドレス：sellerC@example.com
パスワード：password123
※初回ログイン時は認証メールを再送してください
```

## 仕様上の動作

- Sold の商品は「購入手続きへ」ボタンが非表示になります

## テストの実施方法

1. テスト用データベースの準備

MySQL コンテナ上

```
mysql -u root -p
```

パスワードは、root を入力する。

```
CREATE DATABASE demo_test;
SHOW DATABASES;
GRANT ALL PRIVILEGES ON demo_test.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;
```

2. テスト用の.env ファイル作成
   PHP コンテナ上

```
cp .env .env.testing
```

ァイルの作成ができたたら、.env.testing ファイルの文頭部分にある APP_ENV と APP_KEY を編集します。
.env.testing

```
APP_NAME=Laravel
- APP_ENV=local
- APP_KEY=base64:vPtYQu63T1fmcyeBgEPd0fJ+jvmnzjYMaUf7d5iuB+c=
+ APP_ENV=test
+ APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
```

次に、.env.testing にデータベースの接続情報を加えてください。

```
  DB_CONNECTION=mysql_test
  DB_HOST=mysql
  DB_PORT=3306
- DB_DATABASE=laravel_db
+ DB_DATABASE=demo_test
```

```
php artisan key:generate --env=testing
php artisan config:clear
php artisan migrate --env=testing
```

3. テスト実行
   　　テストファイルの場所：src\tests\Feature

PHP コンテナ上

```
php artisan test
```

## 使用技術(実行環境)

- PHP 8.1.33
- Laravel 8.83.8
- MySQL 8.0.26

## ER 図
![image](https://github.com/user-attachments/assets/eb27816c-8277-47d0-ba44-42da91feaec2)
## URL

- phpMyAdmin：http://localhost:8080/

- mailhog：http://localhost:8025/

- フリマアプリ商品一覧画面 :http://localhost/
- ユーザ登録ページ :http://localhost/register
- ログインページ :http://localhost/login
