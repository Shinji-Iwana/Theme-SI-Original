<?php get_header(); ?>

<div id="content" class="clearfix">
	<div id="contentInner">
		<main class="st-main"><?php // メインのランドマーク（ユーザー補助。PageSpeed Insights の landmark-one-main） ?>
			<article>
					<h1> <!--検索結果数-->
						「<?php echo esc_html( get_search_query( false ) ); ?>」の検索結果 <?php echo (int) $GLOBALS['wp_query']->found_posts; ?> 件 </h1>
					<!--検索結果数終わり-->
					<?php get_template_part( 'itiran' ); // 投稿一覧読み込み ?>
					<?php get_template_part( 'st-pagenavi' ); // ページナビ読み込み ?>
			</article>
		</main><!-- /st-main -->
	</div>
	<!-- /#contentInner -->
	<?php get_sidebar(); ?>
</div>
<!--/#content -->
<?php get_footer(); ?>
