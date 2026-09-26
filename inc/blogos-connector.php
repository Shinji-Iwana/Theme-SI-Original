<?php
/**
 * BlogOS連携用の拡張（BlogOS：BLOGOS_WORDPRESS_API.md 第V部、D-01-12、D-28）
 *
 * 1. 投稿メタ `_blogos_draft_id`
 *    BlogOSから新規記事を作成したとき、通信のタイムアウト等で結果が分からなくなった場合に、
 *    作成された投稿を確実に照合するため、BlogOSの編集案のIDをREST APIで読み書きできるようにする。
 *    - メタキーは先頭が `_` のため、WordPressの編集画面の「カスタムフィールド」には表示されない。
 *    - 読み書きできるのは、投稿の編集権限（edit_posts）を持つユーザーだけ。
 *    - BlogOSは、接続確認のときに、この拡張が有効かどうかを判定する（BLOGOS_WORDPRESS_API.md 26章）。
 *
 * 2. プレビュー（保存せずに、渡した内容でページを表示する）
 *    BlogOSの編集案を、実際のテーマ・ショートコード・プラグインで表示して、変更前と比べるため。
 *    - POST /wp-json/blogos/v1/preview（edit_posts の権限が必要）で内容を渡すと、表示用のURLを返す。
 *    - URLのトークンは推測できない値で、1回だけ使え、10分で期限が切れる。
 *    - 記事は保存しない（表示の間だけ、タイトル・本文・抜粋を差し替える）。
 *    - 表示は検索エンジンに登録させず、キャッシュもさせない。
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ---------------------------------------------------------
 * 1. 投稿メタ _blogos_draft_id
 * --------------------------------------------------------- */

add_action('init', function () {
    foreach (['post', 'page'] as $post_type) {
        register_post_meta($post_type, '_blogos_draft_id', [
            'type'          => 'string',
            'single'        => true,
            'show_in_rest'  => true,
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);
    }
});

/* ---------------------------------------------------------
 * 2. プレビュー
 * --------------------------------------------------------- */

if (!defined('BLOGOS_PREVIEW_TTL')) {
    // トークンの有効期限（秒）
    define('BLOGOS_PREVIEW_TTL', 600);
}

if (!function_exists('blogos_preview')) {
    /**
     * 表示中のプレビューの内容（プレビューでなければ null）
     *
     * @return array{post_id: int, post_type: string, title: ?string, content: ?string, excerpt: ?string}|null
     */
    function blogos_preview(): ?array
    {
        return $GLOBALS['blogos_preview'] ?? null;
    }
}

if (!function_exists('blogos_is_preview')) {
    /**
     * BlogOSのプレビューの表示中かどうか（子テーマの表示回数の記録などで、数えないために使う）
     */
    function blogos_is_preview(): bool
    {
        return blogos_preview() !== null;
    }
}

add_action('rest_api_init', function () {
    register_rest_route('blogos/v1', '/preview', [
        'methods'             => 'POST',
        'permission_callback' => fn () => current_user_can('edit_posts'),
        'args'                => [
            'post_id'   => ['type' => 'integer', 'default' => 0],
            'post_type' => ['type' => 'string', 'enum' => ['post', 'page'], 'default' => 'post'],
            // null の項目は差し替えない（変更前の表示）
            'title'     => ['type' => ['string', 'null'], 'default' => null],
            'content'   => ['type' => ['string', 'null'], 'default' => null],
            'excerpt'   => ['type' => ['string', 'null'], 'default' => null],
        ],
        'callback'            => 'blogos_create_preview',
    ]);
});

if (!function_exists('blogos_create_preview')) {
    /**
     * プレビューのトークンを作り、表示用のURLを返す
     *
     * 対象の記事がない（新規記事）・公開されていない場合でも表示できるよう、表示に使う記事（shell）を決める。
     * 新規記事では、同じ種類の最新の公開済みの記事を土台にして、タイトル・本文だけを差し替える。
     */
    function blogos_create_preview(WP_REST_Request $request)
    {
        $post_type = (string) $request['post_type'];
        $post_id = (int) $request['post_id'];

        if ($post_id > 0) {
            $post = get_post($post_id);
            if (!$post instanceof WP_Post || $post->post_type !== $post_type) {
                return new WP_Error('blogos_preview_not_found', '対象の記事が見つかりません。', ['status' => 404]);
            }
            if (!current_user_can('edit_post', $post_id)) {
                return new WP_Error('blogos_preview_forbidden', 'この記事を編集する権限がありません。', ['status' => 403]);
            }
            $shell_id = $post_id;
        } else {
            $latest = get_posts(['post_type' => $post_type, 'post_status' => 'publish', 'numberposts' => 1, 'fields' => 'ids']);
            if ($latest === []) {
                return new WP_Error('blogos_preview_no_shell', '表示の土台にする公開済みの記事がありません。', ['status' => 422]);
            }
            $shell_id = (int) $latest[0];
        }

        $token = wp_generate_password(40, false, false);
        set_transient('blogos_preview_' . $token, [
            'post_id'   => $shell_id,
            'post_type' => $post_type,
            'title'     => $request['title'],
            'content'   => $request['content'],
            'excerpt'   => $request['excerpt'],
        ], BLOGOS_PREVIEW_TTL);

        $query = $post_type === 'page' ? ['page_id' => $shell_id] : ['p' => $shell_id];

        return rest_ensure_response([
            'url'           => add_query_arg($query + ['blogos_preview' => $token], home_url('/')),
            'shell_post_id' => $shell_id,
            'is_new'        => $post_id === 0,
            'expires_in'    => BLOGOS_PREVIEW_TTL,
        ]);
    }
}

// プレビューのURLで開かれたら、トークンを確かめて使い切る（1回だけ使える）
add_action('init', function () {
    if (!isset($_GET['blogos_preview'])) {
        return;
    }

    $token = preg_replace('/[^A-Za-z0-9]/', '', (string) $_GET['blogos_preview']);
    $data = $token !== '' ? get_transient('blogos_preview_' . $token) : false;

    if (!is_array($data)) {
        wp_die('プレビューの期限が切れたか、URLが正しくありません。BlogOSからプレビューを開き直してください。', 'プレビュー', ['response' => 410]);
    }

    delete_transient('blogos_preview_' . $token);
    $GLOBALS['blogos_preview'] = $data;

    // 管理バーを出さない
    add_filter('show_admin_bar', '__return_false');
}, 1);

// 公開されていない記事も表示できるようにする（トークンが許可の代わり）
add_action('pre_get_posts', function (WP_Query $query) {
    if (blogos_is_preview() && $query->is_main_query()) {
        $query->set('post_status', ['publish', 'future', 'draft', 'pending', 'private']);
    }
});

// 表示の間だけ、タイトル・本文・抜粋を差し替える（保存はしない）
add_filter('posts_results', function (array $posts, WP_Query $query) {
    $preview = blogos_preview();
    if ($preview === null || !$query->is_main_query()) {
        return $posts;
    }

    foreach ($posts as $post) {
        if ((int) $post->ID !== (int) $preview['post_id']) {
            continue;
        }
        foreach (['title' => 'post_title', 'content' => 'post_content', 'excerpt' => 'post_excerpt'] as $key => $field) {
            if ($preview[$key] !== null) {
                $post->{$field} = (string) $preview[$key];
            }
        }
    }

    return $posts;
}, 10, 2);

// 正規のURLへの転送をしない（プレビューのトークンが外れないように）
add_filter('redirect_canonical', fn ($redirect) => blogos_is_preview() ? false : $redirect);

// 検索エンジンに登録させず、キャッシュもさせない
add_action('send_headers', function () {
    if (blogos_is_preview()) {
        header('X-Robots-Tag: noindex, nofollow');
        header('Cache-Control: no-store, private');
    }
});
