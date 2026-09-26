<?php get_header(); ?>

<div id="content" class="clearfix">
	<div id="contentInner">
		<div class="st-main">

				<!--ぱんくず -->
				<?php if ( is_category() ) { ?>
					<section id="breadcrumb">
					<ol itemscope itemtype="http://schema.org/BreadcrumbList">
						<li itemprop="itemListElement" itemscope
      itemtype="http://schema.org/ListItem"><a href="<?php echo esc_url( home_url() ); ?>" itemprop="item"><span itemprop="name">HOME</span></a> > <meta itemprop="position" content="1" /></li>
					<?php // カテゴリーが階層化している場合は、親カテゴリーから表示する
					$catid = (int) get_query_var( 'cat' );
					if ( !$catid ) {
						$catid = st_first_category_id();
					}
					$i = 2;
					foreach ( st_breadcrumb_categories( $catid ) as $catid ): ?>
							<li itemprop="itemListElement" itemscope
      itemtype="http://schema.org/ListItem"><a href="<?php echo esc_url( get_category_link( $catid ) ); ?>" itemprop="item">
								<span itemprop="name"><?php echo esc_html( get_cat_name( $catid ) ); ?></span> </a> &gt;
								<meta itemprop="position" content="<?php echo (int) $i; ?>" />
							</li>
					<?php $i++; ?>
					<?php endforeach; ?>
					</ol>
					</section>

				<?php } elseif ( is_tag() ) { // タグアーカイブ ?>
					<section id="breadcrumb">
					<ol>
						<li><a href="<?php echo esc_url( home_url() ); ?>"><span>HOME</span></a> > </li>
						<li><?php single_tag_title(); ?></li>
					</ol>
					</section>
				<?php } elseif ( is_author() ) { // 投稿者アーカイブ ?>
					<section id="breadcrumb">
					<ol>
						<li><a href="<?php echo esc_url( home_url() ); ?>"><span>HOME</span></a> >  </li>
						<li><?php the_author_meta( 'display_name', (int) get_query_var( 'author' ) ); ?></li>
					</ol>
					</section>
				<?php } elseif ( is_attachment() ) { // 添付ファイル
					$attachment = get_queried_object(); ?>
					<section id="breadcrumb">
					<ol>
						<li><a href="<?php echo esc_url( home_url() ); ?>"><span>HOME</span></a> >  </li>
						<?php if ( $attachment instanceof WP_Post && $attachment->post_parent != 0 ): ?> >
							<li><a href="<?php echo esc_url( get_permalink( $attachment->post_parent ) ); ?>"><?php echo esc_html( get_the_title( $attachment->post_parent ) ); ?></a> > </li>
						<?php endif; ?>
							<li><?php echo esc_html( $attachment instanceof WP_Post ? $attachment->post_title : '' ); ?></li>
					</ol>
					</section>
				<?php } elseif ( is_date() ) { // 日付アーカイブ ?>
					<section id="breadcrumb">
					<ol>
						<li><a href="<?php echo esc_url( home_url() ); ?>"><span>HOME</span></a> >  </li>

						<?php $year = (int) get_query_var( 'year' ); $month = (int) get_query_var( 'monthnum' ); ?>
						<?php if ( is_day() ): // 日別アーカイブ ?>
							<li><a href="<?php echo esc_url( get_year_link( $year ) ); ?>"><?php echo $year; ?>年</a> > </li>
							<li><a href="<?php echo esc_url( get_month_link( $year, $month ) ); ?>"><?php echo $month; ?>月</a> > </li>
							<li><?php echo (int) get_query_var( 'day' ); ?>日</li>
						<?php elseif ( is_month() ): // 月別アーカイブ ?>
							<li><a href="<?php echo esc_url( get_year_link( $year ) ); ?>"><?php echo $year; ?>年</a> > </li>
							<li><?php echo $month; ?>月</li>
						<?php elseif ( is_year() ): // 年別アーカイブ ?>
							<li><?php echo $year; ?>年</li>
						<?php endif; ?>
					</ol>
					</section>
				<?php } ?>
				<!--/ ぱんくず -->

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
