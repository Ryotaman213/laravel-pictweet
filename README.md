# Laravel PicTweet

Laravel 5.8.31 で作られた PicTweet を Laravel 13 に更新したアプリケーションです。ユーザー登録、メールアドレスでのログイン、投稿とコメント、ユーザーページ、パスワード再設定を利用できます。

## 必要環境

- PHP 8.3 以上
- Composer 2
- Laravel 13 がサポートするデータベース。MySQL 5.7 以上、MariaDB 10.3 以上、PostgreSQL 10 以上、SQLite 3.26 以上、SQL Server 2017 以上
- PHP 拡張: `curl`, `dom`, `mbstring`, PDO ドライバー、`xml`, `zip`

開発用の既定値は SQLite とファイルセッション・キャッシュです。CI とローカルでは PHP 8.3 / SQLite の動作を確認しています。CI には PHP 8.4 も含めています。実運用の MySQL、実 SMTP はこの更新作業では接続して検証していません。

## ローカルでの起動

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

ブラウザーで `http://127.0.0.1:8000` を開きます。SQLite ファイルはローカル環境で作成してください。別の DB を使う場合は `.env` の `DB_CONNECTION` と接続設定を変更してください。

`php artisan key:generate` は、新しく作るローカル環境でのみ実行してください。既存環境で `APP_KEY` を変えると、暗号化されたデータやログイン中のセッションに影響します。既存環境の更新手順は [実装・運用計画](docs/IMPLEMENTATION_PLAN.md) を参照してください。

## テストと依存関係の監査

```sh
composer validate --strict
php artisan test
composer audit --locked
```

CI は PHP 8.3 / 8.4 で Composer の検証、SQLite のマイグレーション、テスト、ルート・ビューキャッシュの生成、Composer 依存関係監査を実行します。更新時の locked audit は既知の Composer パッケージ脆弱性を検出しませんでした。更新前のロックファイルでは 27 件が検出されていました。この監査結果は Composer が把握しているアドバイザリーの範囲であり、アプリケーション全体に脆弱性がないことを保証するものではありません。

## 運用資料

既存データを保持した更新、バックアップ、`APP_KEY` の保全、ロールバックの手順は [docs/IMPLEMENTATION_PLAN.md](docs/IMPLEMENTATION_PLAN.md) を参照してください。
