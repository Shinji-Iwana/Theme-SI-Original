<?php
/**
 * テンプレートで使う関数と、表示の調整
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('st_is_mobile')) {
    /**
     * スマートフォンかどうか（タブレットは含めない。WordPress の wp_is_mobile はタブレットも含むため、独自に判定する）
     */
    function st_is_mobile(): bool
    {
        $useragents = [
            'iPhone',          // iPhone
            'iPod',            // iPod touch
            'Android.*Mobile', // Android（スマートフォンのみ）
            'Windows.*Phone',  // Windows Phone
            'webOS',
            'incognito',
            'webmate',
        ];

        return (bool) preg_match('/' . implode('|', $useragents) . '/i', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }
}

if (!function_exists('st_custom_excerpt_length')) {
    /**
     * 抜粋の長さ
     */
    function st_custom_excerpt_length(int $length): int
    {
        return 100;
    }
}
add_filter('excerpt_length', 'st_custom_excerpt_length', 999);

if (!function_exists('st_custom_excerpt_more')) {
    /**
     * 抜粋の末尾の文字
     */
    function st_custom_excerpt_more(string $more): string
    {
        return ' ... ';
    }
}
add_filter('excerpt_more', 'st_custom_excerpt_more');

if (!function_exists('st_trim_excerpt')) {
    /**
     * 抜粋を本文から作るときに、ショートコードを処理してから短くする
     */
    function st_trim_excerpt(string $text = ''): string
    {
        if ($text !== '') {
            return $text;
        }

        $text = apply_filters('the_content', get_the_content(''));
        $text = str_replace(']]>', ']]&gt;', $text);

        return wp_trim_words($text, apply_filters('excerpt_length', 55), apply_filters('excerpt_more', ' [&hellip;]'));
    }
}
add_filter('get_the_excerpt', 'st_trim_excerpt', 9);

if (!function_exists('st_custom_content_more_link')) {
    /**
     * 「続きを読む」のリンクから #more-123 を外す
     */
    function st_custom_content_more_link(string $output): string
    {
        return (string) preg_replace('/#more-[\d]+/i', '', $output);
    }
}
add_filter('the_content_more_link', 'st_custom_content_more_link');

if (!function_exists('st_wrap_iframe_in_div')) {
    /**
     * YouTube の iframe を、幅に合わせて縮む枠で囲む
     */
    function st_wrap_iframe_in_div(string $content): string
    {
        return (string) preg_replace('/<iframe[^>]+?youtube\.com[^<]+?<\/iframe>/is', '<div class="youtube-container">${0}</div>', $content);
    }
}

if (!function_exists('st_singular_wrap_iframe_in_div')) {
    /**
     * 個別の記事・固定ページだけ、YouTube の iframe を囲む
     */
    function st_singular_wrap_iframe_in_div(string $content): string
    {
        return is_singular() ? st_wrap_iframe_in_div($content) : $content;
    }
}
add_filter('the_content', 'st_singular_wrap_iframe_in_div');

if (!function_exists('st_get_mtime')) {
    /**
     * 更新日（投稿日と同じ日なら null）
     */
    function st_get_mtime(string $format): ?string
    {
        $mtime = (int) get_the_modified_time('Ymd');
        $ptime = (int) get_the_time('Ymd');

        if ($ptime > $mtime) {
            return get_the_time($format);
        }

        return $ptime === $mtime ? null : get_the_modified_time($format);
    }
}

if (!function_exists('st_rss_feed_copyright')) {
    /**
     * RSS の本文の末尾に著作権の表示を加える
     */
    function st_rss_feed_copyright(string $content): string
    {
        return $content . '<p>Copyright &copy; ' . esc_html(wp_date('Y')) . ' <a href="' . esc_url(home_url()) . '">'
            . esc_html(get_bloginfo('name')) . '</a> All Rights Reserved.</p>';
    }
}
add_filter('the_excerpt_rss', 'st_rss_feed_copyright');
add_filter('the_content_feed', 'st_rss_feed_copyright');

if (!function_exists('st_showads')) {
    /**
     * ショートコード [adsense]：ウィジェットの広告（st-ad.php）を本文に差し込む
     */
    function st_showads(): string
    {
        ob_start();
        get_template_part('st-ad');

        return (string) ob_get_clean();
    }
}
add_shortcode('adsense', 'st_showads');

if (!function_exists('st_breadcrumb_categories')) {
    /**
     * パンくずリストに表示するカテゴリ（上位から順）
     *
     * @return array<int, int> カテゴリのID
     */
    function st_breadcrumb_categories(int $categoryId): array
    {
        $ids = [];
        while ($categoryId !== 0) {
            $category = get_category($categoryId);
            if (!$category instanceof WP_Term) {
                break;
            }
            $ids[] = $category->term_id;
            $categoryId = (int) $category->parent;
        }

        return array_reverse($ids);
    }
}

if (!function_exists('st_first_category_id')) {
    /**
     * 記事の最初のカテゴリのID（カテゴリがなければ 0）
     */
    function st_first_category_id(): int
    {
        $categories = get_the_category();

        return $categories !== [] ? (int) $categories[0]->term_id : 0;
    }
}
