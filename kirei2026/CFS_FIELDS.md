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
| フロアマップ画像 | `floor_map_image` | ファイルアップロード（ループ内、戻り値はURL） |
| フロアマップ画像の説明 | `floor_map_alt` | テキスト（ループ内） |
| フロアマップ凡例 | `floor_map_caption` | テキスト（ループ内） |
| 「みる」タイムスケジュール | `see_schedule` | テキストエリア（ループ内） |
| 「きく」タイムスケジュール | `listen_schedule` | テキストエリア（ループ内） |
| 「ふれる」開催時間 | `touch_schedule` | テキストエリア（ループ内） |
| 注記 | `kirei_schedule_note` | テキストエリア |
| 会場案内：見出し | `kirei_venue_guide_heading` | テキスト |

URLの3項目は空欄なら非表示です。タイムスケジュールは1行に1件、`10:15〜10:30｜素肌感を活かすメイク` の形式で入力します。会場案内は各会場の見出しを開くと、フロアマップとタイムスケジュールを表示します。

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
管理画面のループ行名は、入力内容にかかわらずすべて「開催内容」と表示します。

## 出演者

| ラベル | 名前 | タイプ |
|---|---|---|
| 見出し | `kirei_people_heading` | テキスト |
| 出演者一覧 | `kirei_people_rows` | ループ |
| 関連する開催内容 | `person_program` | セレクト（ループ内） |
| 区分 | `person_label` | テキスト（ループ内） |
| 肩書き | `person_role` | テキスト（ループ内） |
| 氏名 | `person_name` | テキスト（ループ内） |
| プロフィール | `person_profile` | テキストエリア（ループ内） |
| 写真 | `person_image` | ファイルアップロード（ループ内、戻り値はURL） |
| 写真の説明 | `person_image_alt` | テキスト（ループ内） |

`person_program` は `see : みる`、`listen : きく` から選択します。氏名の「さん」はページ側で自動表示します。出演者一覧が空の間はセクションごと非表示です。
管理画面のループ行名は、入力内容にかかわらずすべて「出演者」と表示します。

## ページ末尾

| ラベル | 名前 | タイプ |
|---|---|---|
| クロージングメッセージ | `kirei_closing_message` | テキスト |
