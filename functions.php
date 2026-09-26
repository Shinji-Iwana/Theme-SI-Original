<?php
/**
 * Theme-SI-Original の機能
 *
 * 新しいテーマは、このテーマ（基盤）を複製して作る。
 */

if (!defined('ABSPATH')) {
    exit;
}

// BlogOS連携用の拡張（投稿メタ _blogos_draft_id）
require_once get_template_directory() . '/inc/blogos-connector.php';
