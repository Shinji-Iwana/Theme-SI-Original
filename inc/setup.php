<?php
/**
 * テーマの初期設定
 *
 * メニューの位置・ウィジェットの場所・画像のサイズの名前は、STINGER8 と同じにしている。
 * テーマを切り替えても、WordPress側の設定（メニュー・ウィジェットの配置・作成済みの画像）が引き継がれるようにするため。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($content_width)) {
    $content_width = 700;
}

if (!function_exists('st_after_setup_theme')) {
    /**
     * テーマの機能
     */
    function st_after_setup_theme(): void
    {
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');

        // script・style のタグに type 属性を付けない（見た目には影響しない）
        add_theme_support('html5', ['script', 'style']);

        // カスタム背景
        add_theme_support('custom-background', [
            'default-color' => '#f2f2f2',
        ]);

        // カスタムヘッダー（ヘッダー画像）。既定の画像は親テーマのもの
        add_theme_support('custom-header', [
            'random-default'     => false,
            'width'              => 1060,
            'height'             => 300,
            'flex-height'        => true,
            'flex-width'         => false,
            'default-text-color' => '',
            'header-text'        => false,
            'uploads'            => true,
            'default-image'      => get_template_directory_uri() . '/images/af.png',
        ]);

        // アイキャッチのサムネイル
        add_image_size('st_thumb100', 100, 100, true);
        add_image_size('st_thumb150', 150, 150, true);

        // 管理画面の編集画面のスタイル
        add_editor_style('editor-style.css');
    }
}
add_action('after_setup_theme', 'st_after_setup_theme');

if (!function_exists('st_register_nav_menus')) {
    /**
     * メニューの位置
     */
    function st_register_nav_menus(): void
    {
        register_nav_menus([
            'primary-menu'    => 'ヘッダー用メニュー',
            'secondary-menu'  => 'フッター用メニュー',
            'smartphone-menu' => 'スマートフォン用メニュー',
        ]);
    }
}
add_action('after_setup_theme', 'st_register_nav_menus');

if (!function_exists('st_register_sidebars')) {
    /**
     * ウィジェットの場所
     */
    function st_register_sidebars(): void
    {
        register_sidebar([
            'id'            => 'sidebar-10',
            'name'          => 'サイドバートップ',
            'description'   => 'サイドバーの一番上に表示されるコンテンツエリアです。（タイトルは表示されません）',
            'before_widget' => '<div class="ad">',
            'after_widget'  => '</div>',
            'before_title'  => '<p style="display:none">',
            'after_title'   => '</p>',
        ]);

        register_sidebar([
            'id'            => 'sidebar-1',
            'name'          => 'サイドバーウイジェット',
            'description'   => 'サイドバーに表示されるコンテンツです',
            'before_widget' => '<div class="ad">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="menu_underh2">',
            'after_title'   => '</p>',
        ]);

        register_sidebar([
            'id'            => 'sidebar-2',
            'name'          => 'スクロール広告用',
            'description'   => 'サイドバーの下でコンテンツに追尾するボックスエリアです。「テキスト」をここにドロップして内容を入力して下さい。アドセンスは禁止です。※PC以外では非表示部分',
            'before_widget' => '<div class="ad">',
            'after_widget'  => '</div>',
            'before_title'  => '<p class="menu_underh2" style="text-align:left;">',
            'after_title'   => '</p>',
        ]);

        register_sidebar([
            'id'            => 'sidebar-3',
            'name'          => '広告・Googleアドセンス用336px',
            'description'   => 'Googleアドセンス336pxに適したボックスで記事下に2つ連続で表示されます。「テキスト」をここにドロップしてコードを入力して下さい。※タイトルは反映されません',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<p style="display:none">',
            'after_title'   => '</p>',
        ]);

        register_sidebar([
            'id'            => 'sidebar-4',
            'name'          => '広告・Googleアドセンスのスマホ用300px',
            'description'   => 'Googleアドセンス300pxに適したボックスで記事下に1つサイドバーの上に１つショートコードを利用した時のアドセンス時にも挿入されます。「テキスト」をここにドロップしてコードを入力して下さい。タイトルは反映されません。',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '<p style="display:none">',
            'after_title'   => '</p>',
        ]);

        register_sidebar([
            'id'            => 'sidebar-9',
            'name'          => '広告・スマホ用記事下のみ',
            'description'   => 'スマホのみ記事下に表示されるボックスエリアです。',
            'before_widget' => '<div class="headbox">',
            'after_widget'  => '</div>',
            'before_title'  => '<p style="display:none">',
            'after_title'   => '</p>',
        ]);
    }
}
add_action('widgets_init', 'st_register_sidebars');

// wp_head の不要な出力を減らす（フィードのリンクは header.php で出力している）
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', 10);
remove_action('wp_head', 'start_post_rel_link', 10);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'wp_generator');

if (!function_exists('st_no_self_ping')) {
    /**
     * 自分のサイトへのピンバックを送らない
     *
     * STINGER8 では apply_filters を使っていたため動いていなかった。pre_ping で正しく除く。
     *
     * @param array<int, string> $links
     */
    function st_no_self_ping(array &$links): void
    {
        $home = home_url();
        foreach ($links as $index => $link) {
            if (str_starts_with($link, $home)) {
                unset($links[$index]);
            }
        }
    }
}
add_action('pre_ping', 'st_no_self_ping');
