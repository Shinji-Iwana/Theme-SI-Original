<?php
/**
 * 管理画面の編集画面（旧エディタ）
 *
 * 書式のプルダウンとクイックタグで、STINGER8 の装飾用のクラス（黄色ボックスなど）を入れられるようにする。
 * ブログ独自のボタンは、子テーマで st_add_orignal_quicktags を置き換えるか、プラグイン AddQuicktag で加える。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('st_custom_editor_settings')) {
    /**
     * 編集画面の本文に、公開ページと同じ id・class を付ける（editor-style.css を効かせるため）
     *
     * @param array<string, mixed> $init
     * @return array<string, mixed>
     */
    function st_custom_editor_settings(array $init): array
    {
        $init['body_id'] = 'primary';
        $init['body_class'] = 'post';

        return $init;
    }
}
add_filter('tiny_mce_before_init', 'st_custom_editor_settings');

if (!function_exists('st_tiny_mce_before_init')) {
    /**
     * 書式のプルダウン
     *
     * @param array<string, mixed> $init
     * @return array<string, mixed>
     */
    function st_tiny_mce_before_init(array $init): array
    {
        $init['block_formats'] = '段落=p;見出し2=h2;見出し3=h3;見出し4=h4;見出し5=h5;見出し6=h6';
        $init['fontsize_formats'] = '70% 80% 90% 120% 130% 150% 200% 250% 300%';

        $init['style_formats'] = wp_json_encode([
            ['title' => '太字', 'inline' => 'span', 'classes' => 'huto'],
            ['title' => '太字（赤）', 'inline' => 'span', 'classes' => 'hutoaka'],
            ['title' => '大文字', 'inline' => 'span', 'classes' => 'oomozi'],
            ['title' => '小文字', 'inline' => 'span', 'classes' => 'komozi'],
            ['title' => 'ドット線', 'inline' => 'span', 'classes' => 'dotline'],
            ['title' => '黄マーカー', 'inline' => 'span', 'classes' => 'ymarker'],
            ['title' => '赤マーカー', 'inline' => 'span', 'classes' => 'rmarker'],
            ['title' => '参考', 'inline' => 'span', 'classes' => 'sankou'],
            ['title' => '写真に枠線', 'inline' => 'span', 'classes' => 'photoline'],
            ['title' => '記事タイトルデザイン', 'block' => 'p', 'classes' => 'entry-title'],
            ['title' => 'code', 'inline' => 'code'],
            ['title' => '吹き出し', 'block' => 'p', 'classes' => 'h2fuu'],
            ['title' => '回り込み解除', 'block' => 'div', 'classes' => 'clearfix', 'wrapper' => true],
            ['title' => 'センター寄せ', 'block' => 'div', 'classes' => 'center', 'wrapper' => true],
            ['title' => '黄色ボックス', 'block' => 'div', 'classes' => 'yellowbox', 'wrapper' => true],
            ['title' => '薄赤ボックス', 'block' => 'div', 'classes' => 'redbox', 'wrapper' => true],
            ['title' => 'グレーボックス', 'block' => 'div', 'classes' => 'graybox', 'wrapper' => true],
            ['title' => '引用風ボックス', 'block' => 'div', 'classes' => 'inyoumodoki', 'wrapper' => true],
            ['title' => 'olタグを囲む数字ボックス', 'block' => 'div', 'classes' => 'maruno', 'wrapper' => true],
            ['title' => 'ulタグを囲む数字ボックス', 'block' => 'div', 'classes' => 'maruck', 'wrapper' => true],
            ['title' => 'table横スクロールボックス', 'block' => 'div', 'classes' => 'scroll-box', 'wrapper' => true],
            ['title' => 'imgインラインボックス', 'block' => 'span', 'classes' => 'inline-img', 'wrapper' => true],
            ['title' => 'width100%リセット', 'block' => 'span', 'classes' => 'resetwidth', 'wrapper' => true],
            ['title' => '装飾なしテーブル', 'block' => 'div', 'classes' => 'notab', 'wrapper' => true],
        ]);

        // WordPress の既定の書式と混ぜない（STINGER8 では変数名の誤りで効いていなかった）
        $init['style_formats_merge'] = false;

        return $init;
    }
}
add_filter('tiny_mce_before_init', 'st_tiny_mce_before_init');

if (!function_exists('st_add_orignal_quicktags')) {
    /**
     * テキストエディタのクイックタグ
     */
    function st_add_orignal_quicktags(): void
    {
        if (!wp_script_is('quicktags')) {
            return;
        }
        ?>
        <script>
            QTags.addButton('ed_p', 'P', '<p>', '</p>');
            QTags.addButton('ed_huto', '太字', '<span class="huto">', '</span>');
            QTags.addButton('ed_hutoaka', '太字（赤）', '<span class="hutoaka">', '</span>');
            QTags.addButton('ed_oomozi', '大文字', '<span class="oomozi">', '</span>');
            QTags.addButton('ed_komozi', '小文字', '<span class="komozi">', '</span>');
            QTags.addButton('ed_dotline', 'ドット線', '<span class="dotline">', '</span>');
            QTags.addButton('ed_ymarker', '黄マーカー', '<span class="ymarker">', '</span>');
            QTags.addButton('ed_rmarker', '赤マーカー', '<span class="rmarker">', '</span>');
            QTags.addButton('ed_sankou', '参考', '<span class="sankou">', '</span>');
            QTags.addButton('ed_photoline', '写真に枠線', '<span class="photoline">', '</span>');
            QTags.addButton('ed_entry', '記事タイトルデザイン', '<p class="entry-title">', '</p>');
            QTags.addButton('ed_code', 'code', '<code>', '</code>');
            QTags.addButton('ed_ads', 'アドセンス', '[adsense]', '');
            QTags.addButton('ed_clearfix', '回り込み解除', '<div class="clearfix">', '</div>');
            QTags.addButton('ed_center', 'センター寄せ', '<div class="center">', '</div>');
            QTags.addButton('ed_yellowbox', '黄色ボックス', '<div class="yellowbox">', '</div>');
            QTags.addButton('ed_redbox', '薄赤ボックス', '<div class="redbox">', '</div>');
            QTags.addButton('ed_graybox', 'グレーボックス', '<div class="graybox">', '</div>');
            QTags.addButton('ed_inyoumodoki', '引用風', '<div class="inyoumodoki">', '</div>');
            QTags.addButton('ed_maruno', 'olタグを囲む数字ボックス', '<div class="maruno">', '</div>');
            QTags.addButton('ed_maruck', 'ulタグを囲むチェックボックス', '<div class="maruck">', '</div>');
            QTags.addButton('ed_scroll_box', 'table横スクロール要素', '<div class="scroll-box">', '</div>');
            QTags.addButton('ed_resetwidth', 'width100%リセット', '<span class="resetwidth">', '</span>');
            QTags.addButton('ed_notab', '装飾なしテーブル', '<div class="notab">', '</div>');
            QTags.addButton('ed_responbox', 'PCのみ左右%ボックス', '<div class="clearfix responbox"><div class="lbox"><p>左側のコンテンツ40%</p></div><div class="rbox"><p>右側のコンテンツ60%</p></div></div>', '');
            QTags.addButton('ed_responbox50s', '全サイズ左右50%ボックス', '<div class="clearfix responbox50 smart50"><div class="lbox"><p>左側のコンテンツ50%</p></div><div class="rbox"><p>右側のコンテンツ50%</p></div></div>', '');
            QTags.addButton('ed_nofollow', 'nofollow', ' rel="nofollow"', '');
        </script>
        <?php
    }
}
add_action('admin_print_footer_scripts', 'st_add_orignal_quicktags');
