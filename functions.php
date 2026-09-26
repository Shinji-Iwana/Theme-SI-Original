<?php
/**
 * Theme-SI-Original（親テーマ）の機能
 *
 * ブログごとのテーマは、このテーマを親にした子テーマとして作る（例：Theme-SI-Note）。
 * 子テーマの functions.php は、このファイルより先に読み込まれる。ここで定義する関数は
 * すべて function_exists で囲んでいるため、子テーマで同じ名前の関数を定義すれば置き換えられる。
 *
 * 元のテーマ：STINGER8（Version 20171207、ENJI 氏）
 */

if (!defined('ABSPATH')) {
    exit;
}

$si_original_dir = get_template_directory();

// テーマの初期設定（テーマの機能・メニューの位置・画像のサイズ・ウィジェットの場所）
require_once $si_original_dir . '/inc/setup.php';

// CSS・JavaScript の読み込み
require_once $si_original_dir . '/inc/enqueue.php';

// テンプレートで使う関数と、表示の調整（抜粋・更新日・iframe など）
require_once $si_original_dir . '/inc/template-functions.php';

// 管理画面の編集画面（旧エディタの書式・クイックタグ・エディタのスタイル）
require_once $si_original_dir . '/inc/editor.php';

// BlogOS との連携（投稿メタ _blogos_draft_id。どの子テーマでも有効にするため、親テーマに置く）
require_once $si_original_dir . '/inc/blogos-connector.php';

unset($si_original_dir);
