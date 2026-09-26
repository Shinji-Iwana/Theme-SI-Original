<?php
/**
 * BlogOS連携用の拡張（BlogOS：BLOGOS_WORDPRESS_API.md 第V部、D-01-12）
 *
 * BlogOSから新規記事を作成したとき、通信のタイムアウト等で結果が分からなくなった場合に、
 * 作成された投稿を確実に照合するため、投稿メタ `_blogos_draft_id`（BlogOSの編集案のID）を
 * REST APIで読み書きできるようにする。
 *
 * - メタキーは先頭が `_` のため、WordPressの編集画面の「カスタムフィールド」には表示されない。
 * - 読み書きできるのは、投稿の編集権限（edit_posts）を持つユーザーだけ。
 * - BlogOSは、接続確認のときに、この拡張が有効かどうかを判定する（BLOGOS_WORDPRESS_API.md 26章）。
 */

if (!defined('ABSPATH')) {
    exit;
}

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
