# Kirei 2026 CFS設定

フィールドグループ名は「Kirei 2026」、配置ルールは `/kirei2026/` の固定ページ（テンプレート「Kirei 2026」）に設定します。

公開中の日時・開催場所はCFSへ登録済みです。各セクションは、対応するフィールドまたはループを編集すると表示へ反映されます。

## メインビジュアル

メインビジュアルは `assets/image/kirei2026/lirei2026-logo.png` を使用するため、CFS項目はありません。

## 日時・開催場所

| ラベル | 名前 | タイプ |
|---|---|---|
| セクション見出し | `kirei_schedule_heading` | テキスト |
| 会場一覧 | `kirei_schedule_rows` | ループ |
| 会場名 | `schedule_area` | テキスト（ループ内） |
| 開催日 | `schedule_date` | テキスト（ループ内） |
| 曜日 | `schedule_weekday` | テキスト（ループ内） |
| 時間 | `schedule_time` | テキスト（ループ内） |
| 会場 | `schedule_venue` | テキスト（ループ内） |
| アクセスURL | `access_url` | テキスト（ループ内） |
| フロアマップURL | `floor_map_url` | テキスト（ループ内） |
| タイムスケジュールURL | `timetable_url` | テキスト（ループ内） |
| 注記 | `kirei_schedule_note` | テキストエリア |

URLの3項目は空欄なら非表示です。後日情報が揃った会場から順に追加できます。

## 開催内容

| ラベル | 名前 | タイプ |
|---|---|---|
| セクション見出し | `kirei_program_heading` | テキスト |
| 開催内容一覧 | `kirei_program_rows` | ループ |
| キーワード | `program_keyword` | テキスト（ループ内） |
| リード | `program_lead` | テキスト（ループ内） |
| 内容名 | `program_title` | テキスト（ループ内） |
| 説明 | `program_description` | テキストエリア（ループ内） |
| 画像 | `program_image` | ファイルアップロード（ループ内、戻り値はURL） |
| 画像alt | `program_image_alt` | テキスト（ループ内） |
| アクセント色 | `program_color` | セレクト（ループ内） |
| 注記 | `kirei_program_note` | テキストエリア |

`program_color` の選択肢は `rose : ピンク`、`green : グリーン`、`blue : ブルー` とします。

## 出演者プロフィール（後日公開用）

| ラベル | 名前 | タイプ |
|---|---|---|
| 見出し | `kirei_guest_heading` | テキスト |
| 氏名 | `kirei_guest_name` | テキスト |
| 肩書き | `kirei_guest_role` | テキスト |
| プロフィール | `kirei_guest_profile` | リッチエディタ |
| 写真 | `kirei_guest_image` | ファイルアップロード（戻り値はURL） |

氏名・プロフィール・写真がすべて空欄の間は、セクションごと非表示です。

## ページ末尾

| ラベル | 名前 | タイプ |
|---|---|---|
| クロージングメッセージ | `kirei_closing_message` | テキスト |
