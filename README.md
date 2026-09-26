# Theme-SI-Original

独自テーマの**親テーマ**（共通の土台）。ブログごとのテーマは、このテーマを親にした**子テーマ**として作る（例：Theme-SI-Note）。

元のテーマ：STINGER8（Version 20171207、ENJI 氏 https://wp-fun.com/ ）。見た目を変えないため、テンプレートのHTMLの構造・クラス名と `style.css` のスタイルは STINGER8 のものを引き継いでいる。

## 動作環境

- WordPress 7.0 以上
- PHP 8.1 以上

## 構成

| ファイル | 内容 |
| --- | --- |
| `style.css` | テーマの情報と、STINGER8 のスタイル |
| `functions.php` | `inc/` のファイルを読み込むだけ |
| `inc/setup.php` | テーマの機能・メニューの位置・ウィジェットの場所・画像のサイズ |
| `inc/enqueue.php` | CSS・JavaScript の読み込み（子テーマの `style.css` も読み込む） |
| `inc/template-functions.php` | テンプレートで使う関数と、表示の調整 |
| `inc/editor.php` | 管理画面の編集画面（旧エディタの書式・クイックタグ） |
| `inc/blogos-connector.php` | BlogOS との連携（投稿メタ `_blogos_draft_id`） |
| `*.php`（直下） | テンプレート |
| `css/`・`js/`・`images/` | STINGER8 の CSS（normalize・Font Awesome 4.5）・JavaScript・画像 |

## 子テーマの作り方

子テーマのフォルダに、次の `style.css` を置く。`Template` は、この親テーマのフォルダ名にする。

```css
/*
Theme Name: Theme-SI-Note
Template: Theme-SI-Original
Version: 1.0.0
*/
```

- 子テーマの `style.css` は、親テーマの `style.css` の後に自動で読み込まれる（子テーマの `functions.php` で読み込む必要はない）。
- テンプレートは、子テーマに同じ名前のファイルを置くと置き換えられる（例：`home.php`）。
- 親テーマの関数は、すべて `function_exists` で囲んでいる。子テーマの `functions.php` で同じ名前の関数を定義すると置き換えられる（子テーマの `functions.php` が先に読み込まれるため）。

## そろえている名前（テーマを切り替えても設定を引き継ぐため）

| 種類 | 名前 |
| --- | --- |
| メニューの位置 | `primary-menu`（ヘッダー）・`secondary-menu`（フッター）・`smartphone-menu`（スマートフォン） |
| ウィジェットの場所 | `sidebar-10`・`sidebar-1`・`sidebar-2`・`sidebar-3`・`sidebar-4`・`sidebar-9` |
| 画像のサイズ | `st_thumb100`（100×100）・`st_thumb150`（150×150） |

## STINGER8 から変えたこと

- jQuery を WordPress 標準のものにした（STINGER8 は Google の配信する jQuery 1.11.3 に差し替えていた）。`js/base.js` の一部が `$` を使うため、`$` を jQuery につないでいる。
- 検索エンジン向けの robots（noindex）の出力をやめた（SEO プラグインが出力するため。二重になるのを防ぐ）。
- 古いブラウザ（Internet Explorer 8 以前）向けの記述と `html5shiv.js` を外した。
- SNSボタンから、終了したサービス（Google+・Pocket）を外した（PC表示の1行のボタンの数を3→4にした）。
- `wp_body_open()` を加えた（プラグインが `<body>` の直後に出力するため）。
- 出力のエスケープ、PHP 8 での警告（カテゴリのない記事、ユーザーエージェントのないアクセス）を直した。
- 動いていなかった処理を直した：自分のサイトへのピンバックの防止、編集画面の書式のプルダウン。
- 使われていなかったものを外した：ショートコード `[originalsc]`（テンプレートがない）、固定ページの `newpost-page`（テンプレートがない）、クイックタグ「イベント」（旧 Google アナリティクス用）。
- CSS・JavaScript のバージョンをファイルの更新日時にした（更新したらブラウザのキャッシュが切り替わる）。

## ライセンス

GNU General Public License v2 or later。STINGER8 をもとにしているため、元の作者の表記を残している。
