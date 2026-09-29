<?php get_header(); ?>

<div id="content" class="clearfix">
	<div id="contentInner">
		<div class="st-main">

				<?php st_breadcrumb(); // ぱんくず（inc/template-functions.php） ?>

			<article>

				<!--ループ開始-->
				<h1 class="entry-title">「
					<?php if ( is_category() ) { ?>
						<?php single_cat_title(); ?>
					<?php } elseif ( is_tag() ) { ?>
						<?php single_tag_title(); ?>
					<?php } elseif ( is_tax() ) { ?>
						<?php single_term_title(); ?>
					<?php } elseif ( is_day() ) { ?>
						日別アーカイブ：<?php echo esc_html( get_the_time( 'Y年m月d日' ) ); ?>
					<?php } elseif ( is_month() ) { ?>
						月別アーカイブ：<?php echo esc_html( get_the_time( 'Y年m月' ) ); ?>
					<?php } elseif ( is_year() ) { ?>
						年別アーカイブ：<?php echo esc_html( get_the_time( 'Y年' ) ); ?>
					<?php } elseif ( is_author() ) { ?>
						投稿者アーカイブ：<?php echo esc_html( get_queried_object()->display_name ?? '' ); ?>
					<?php } elseif ( is_paged() ) { ?>
						ブログアーカイブ
					<?php } ?>
					」 一覧 </h1>

				<?php get_template_part( 'itiran' ); // 投稿一覧読み込み ?>
				<?php get_template_part( 'st-pagenavi' ); // ページナビ読み込み ?>

			</article>
		</div>
	</div>
	<!-- /#contentInner -->
	<?php get_sidebar(); ?>
</div>
<!--/#content -->
<?php get_footer(); ?>
