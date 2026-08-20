# Kirei 2026

三基商事様 WordPressサイトの `/kirei2026/` 専用テンプレートと管理画面設定です。

## 追跡対象

- `page-kirei2026.php`: 固定ページテンプレート
- `assets/css/kirei2026.css`: ページ専用CSS
- `assets/js/kirei2026.js`: ページ専用JavaScript
- `assets/image/kirei2026/`: ページ専用画像
- `kirei2026/CFS_FIELDS.md`: CFSフィールド仕様
- `kirei2026/sync-wordpress.php`: 固定ページ・CFS設定のCLI同期

既存サイト一式、認証情報、`wp-config.php`、バックアップ、制作素材はGit管理の対象外です。

## WordPress同期

状態確認：

```sh
php kirei2026/sync-wordpress.php --wp-load=/path/to/wp-load.php
```

反映：

```sh
php kirei2026/sync-wordpress.php --wp-load=/path/to/wp-load.php --apply
```

スクリプトはCLI専用です。固定ページとCFSグループが存在しない場合に作成し、日時・会場情報が未登録の場合のみ初期データを管理画面へ登録します。
