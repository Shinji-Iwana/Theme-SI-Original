<?php
/**
 * CSS・JavaScript の読み込み
 *
 * - jQuery は WordPress 標準のものを使う（STINGER8 は Google の配信する jQuery 1.11.3 に差し替えていた）。
 * - 親テーマの style.css の後に、子テーマの style.css を読み込む。
 * - ファイルの更新日時をバージョンにして、更新したらブラウザのキャッシュが切り替わるようにする。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('st_asset_version')) {
    /**
     * ファイルの更新日時（ファイルがなければテーマのバージョン）
     */
    function st_asset_version(string $path): string
    {
        return is_file($path) ? (string) filemtime($path) : (string) wp_get_theme()->get('Version');
    }
}

if (!function_exists('st_enqueue_scripts')) {
    /**
     * JavaScript
     */
    function st_enqueue_scripts(): void
    {
        $dir = get_template_directory();
        $uri = get_template_directory_uri();

        wp_enqueue_script('jquery');

        // jQuery は、ページの最後で読み込む（<head> で読み込むと、読み込み終わるまで表示が止まるため。PageSpeed Insights の「レンダリングを妨げるリクエスト」）。
        // jQuery を使うのは、ページの最後で読み込む base.js・scroll.js とプラグインの JavaScript だけで、本文の中では使っていない
        foreach (['jquery', 'jquery-core', 'jquery-migrate'] as $handle) {
            wp_script_add_data($handle, 'group', 1);
        }

        // base.js の一部は、jQuery を $ で呼んでいる（WordPress 標準の jQuery では $ は使えないため、つなぐ）
        wp_add_inline_script('jquery', 'window.$ = window.$ || window.jQuery;');

        wp_enqueue_script('st-base', $uri . '/js/base.js', ['jquery'], st_asset_version($dir . '/js/base.js'), true);

        // サイドバーの追尾広告
        wp_enqueue_script('st-scroll', $uri . '/js/scroll.js', ['jquery'], st_asset_version($dir . '/js/scroll.js'), true);

        // コメントの返信
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}
add_action('wp_enqueue_scripts', 'st_enqueue_scripts');

if (!function_exists('st_enqueue_styles')) {
    /**
     * CSS
     */
    function st_enqueue_styles(): void
    {
        $dir = get_template_directory();
        $uri = get_template_directory_uri();

        wp_register_style('normalize', $uri . '/css/normalize.css', [], '1.5.9');
        // Font Awesome 4.6.3。@font-face に font-display: swap を足している（文字を先に表示する。PageSpeed Insights の「フォントの表示」）
        wp_register_style('font-awesome', $uri . '/css/fontawesome/css/font-awesome.min.css', ['normalize'], st_asset_version($dir . '/css/fontawesome/css/font-awesome.min.css'));

        // 親テーマの style.css
        wp_enqueue_style('st-style', $uri . '/style.css', ['normalize', 'font-awesome'], st_asset_version($dir . '/style.css'));
    }
}
add_action('wp_enqueue_scripts', 'st_enqueue_styles');

if (!function_exists('st_non_blocking_style_tag')) {
    /**
     * 表示を止めずに読み込む CSS
     *
     * 最初の表示に要らない CSS は、media="print" で読み込み、読み込み終わったら全部の画面に効かせる。
     * <head> の CSS は、読み込み終わるまで表示が止まるため（PageSpeed Insights の「レンダリングを妨げるリクエスト」）。
     * 対象は、フィルター st_non_blocking_styles のハンドル名（子テーマから足す）。
     * Font Awesome は対象にしない（メニューのボタンや日付のアイコンが後から現れ、表示がずれるため）。
     */
    function st_non_blocking_style_tag(string $tag, string $handle): string
    {
        $handles = (array) apply_filters('st_non_blocking_styles', []);

        if (!in_array($handle, $handles, true) || str_contains($tag, 'onload=')) {
            return $tag;
        }

        $async = preg_replace("/\smedia=(['\"])all\\1/", " media='print' onload=\"this.media='all'\"", $tag, 1);

        // JavaScript が動かないときは、そのまま読み込む
        return $async === $tag ? $tag : $async . '<noscript>' . trim($tag) . "</noscript>\n";
    }
}
add_filter('style_loader_tag', 'st_non_blocking_style_tag', 10, 2);

if (!function_exists('st_enqueue_child_style')) {
    /**
     * 子テーマの style.css
     *
     * プラグインの CSS（多くは優先度 10 で登録される）より後に読み込むため、優先度 20 にする。
     * WordPress の「追加CSS」と同じように、プラグインの CSS を子テーマで上書きできるようにするため。
     */
    function st_enqueue_child_style(): void
    {
        if (is_child_theme()) {
            wp_enqueue_style('st-child-style', get_stylesheet_uri(), ['st-style'], st_asset_version(get_stylesheet_directory() . '/style.css'));
        }
    }
}
add_action('wp_enqueue_scripts', 'st_enqueue_child_style', 20);
