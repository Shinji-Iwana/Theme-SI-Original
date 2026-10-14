<?php get_header(); ?>

<div id="content" class="clearfix">
	<div id="contentInner">
		<main class="st-main"><?php // メインのランドマーク（ユーザー補助。PageSpeed Insights の landmark-one-main） ?>
			<article>
				<div class="st-aside">
					<?php get_template_part( 'itiran' ); ?>
					<?php get_template_part( 'st-pagenavi' ); //ページナビ読み込み ?>
				</div>

					<?php get_template_part( 'sns-top' ); //ソーシャルボタン読み込み ?>

			</article>
		</main><!-- /st-main -->
	</div>
	<!-- /#contentInner -->
	<?php get_sidebar(); ?>
</div>
<!-- /#content -->
<?php get_footer(); ?>