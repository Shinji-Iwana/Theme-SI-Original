<?php
/**
 * コメント
 */
if ( post_password_required() ) {
	return;
}
?>
<hr class="hrcss" />
<div id="comments">
	<?php if ( have_comments() ): ?>
		<ol class="commets-list">
			<?php wp_list_comments( array( 'avatar_size' => 55 ) ); ?>
		</ol>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply'        => 'comment',
		'label_submit'       => 'コメントを送る',
		'title_reply_before' => '<p id="st-reply-title" class="comment-reply-title">',
		'title_reply_after'  => '</p>',
	) );
	?>
</div>
<?php if ( get_comment_pages_count() > 1 ): ?>
<div class="st-pagelink">
	<?php
	paginate_comments_links( array(
		'prev_text' => '&laquo; Prev',
		'next_text' => 'Next &raquo;',
	) );
	?>
</div>
<?php endif; ?>
