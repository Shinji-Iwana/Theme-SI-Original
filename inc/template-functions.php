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

if (!function_exists('st_breadcrumb_items')) {
    /**
     * パンくずリストの項目（HOME から今のページまで）
     *
     * 最後の項目（今のページ）は URL なし。子テーマは、フィルター st_breadcrumb_items で項目を変えられる
     * （category_id・page_id は、置き換えの目印。表示には使わない）。
     *
     * @return list<array{name: string, url: ?string, category_id?: int, page_id?: int}>
     */
    function st_breadcrumb_items(): array
    {
        $items = [['name' => 'HOME', 'url' => home_url('/')]];

        if (is_single() && !is_attachment()) {
            foreach (st_breadcrumb_categories(st_first_category_id()) as $categoryId) {
                $items[] = ['name' => get_cat_name($categoryId), 'url' => get_category_link($categoryId), 'category_id' => $categoryId];
            }
            $items[] = ['name' => get_the_title(get_queried_object_id()), 'url' => null];
        } elseif (is_page() && !is_front_page()) {
            $pageId = get_queried_object_id();
            foreach (array_reverse(get_post_ancestors($pageId)) as $ancestorId) {
                $items[] = ['name' => get_the_title($ancestorId), 'url' => get_page_link($ancestorId), 'page_id' => (int) $ancestorId];
            }
            $items[] = ['name' => get_the_title($pageId), 'url' => null, 'page_id' => $pageId];
        } elseif (is_category()) {
            foreach (st_breadcrumb_categories((int) get_query_var('cat')) as $categoryId) {
                $items[] = ['name' => get_cat_name($categoryId), 'url' => get_category_link($categoryId), 'category_id' => $categoryId];
            }
        } elseif (is_tag()) {
            $items[] = ['name' => single_tag_title('', false), 'url' => null];
        } elseif (is_author()) {
            $items[] = ['name' => get_the_author_meta('display_name', (int) get_query_var('author')), 'url' => null];
        } elseif (is_attachment()) {
            $attachment = get_queried_object();
            if ($attachment instanceof WP_Post && $attachment->post_parent != 0) {
                $items[] = ['name' => get_the_title($attachment->post_parent), 'url' => get_permalink($attachment->post_parent)];
            }
            $items[] = ['name' => $attachment instanceof WP_Post ? $attachment->post_title : '', 'url' => null];
        } elseif (is_date()) {
            $year = (int) get_query_var('year');
            $month = (int) get_query_var('monthnum');
            $items[] = ['name' => "{$year}年", 'url' => get_year_link($year)];
            if (is_month() || is_day()) {
                $items[] = ['name' => "{$month}月", 'url' => get_month_link($year, $month)];
            }
            if (is_day()) {
                $items[] = ['name' => (int) get_query_var('day') . '日', 'url' => null];
            }
        } elseif (is_search()) {
            $items[] = ['name' => '「' . get_search_query() . '」の検索結果', 'url' => null];
        }

        $items = apply_filters('st_breadcrumb_items', $items);

        // 最後の項目（今のページ）は、リンクにしない
        $last = count($items) - 1;
        if ($last > 0) {
            $items[$last]['url'] = null;
        }

        return $items;
    }
}

if (!function_exists('st_breadcrumb')) {
    /**
     * パンくずリスト（schema.org の BreadcrumbList。区切りの「>」は項目の間だけ）
     */
    function st_breadcrumb(): void
    {
        $items = st_breadcrumb_items();
        if (count($items) < 2) {
            return;
        }

        echo '<section id="breadcrumb"><ol itemscope itemtype="https://schema.org/BreadcrumbList">';
        foreach ($items as $index => $item) {
            echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            if ($index > 0) {
                echo '<span class="breadcrumb-sep" aria-hidden="true">&gt;</span> ';
            }
            $name = '<span itemprop="name">' . esc_html($item['name']) . '</span>';
            echo $item['url'] !== null
                ? '<a href="' . esc_url($item['url']) . '" itemprop="item">' . $name . '</a>'
                : $name;
            echo '<meta itemprop="position" content="' . ($index + 1) . '" /></li> ';
        }
        echo '</ol></section>';
    }
}
