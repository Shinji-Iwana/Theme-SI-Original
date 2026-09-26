<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
		<meta charset="<?php bloginfo( 'charset' ); ?>" >
		<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes">
		<meta name="format-detection" content="telephone=no" >
		<?php
		/*
		 * 検索エンジン向けの robots（noindex）と OGP は、SEO プラグイン（All in One SEO）が出力する。
		 * STINGER8 はテーマでも robots を出力していたが、プラグインと二重になるため出力しない。
		 * 広告・アクセス解析のコードも、テーマには書かない（Site Kit 等のプラグインで入れる）。
		 */
		?>
		<link rel="alternate" type="application/rss+xml" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> RSS Feed" href="<?php echo esc_url( get_bloginfo( 'rss2_url' ) ); ?>" />
		<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>" >
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?> >
		<?php wp_body_open(); ?>
			<div id="st-ami">
				<div id="wrapper">
				<div id="wrapper-in">
					<header>
						<div id="headbox-bg">
							<div class="clearfix" id="headbox">
								<?php get_template_part( 'st-accordion-menu' ); // アコーディオンメニュー ?>
									<div id="header-l">
									<!-- ロゴ又はブログ名 -->
									<p class="sitename">
										<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
											<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
										</a>
									</p>
									<!-- ロゴ又はブログ名ここまで -->
									<!-- キャプション -->
									<?php if ( is_front_page() ) { ?>
										<h1 class="descr">
											<?php bloginfo( 'description' ); ?>
										</h1>
									<?php } else { ?>
										<p class="descr">
											<?php bloginfo( 'description' ); ?>
										</p>
									<?php } ?>
									</div><!-- /#header-l -->

							</div><!-- /#headbox-bg -->
						</div><!-- /#headbox clearfix -->

						<div id="gazou-wide">
							<?php get_template_part( 'st-header-menu' ); // カスタムヘッダーメニュー ?>

							<?php if ( get_header_image() && is_front_page() ) : // カスタムヘッダー ?>
							<div id="st-headerbox">
								<div id="st-header">
									<img src="<?php header_image(); ?>" height="<?php echo esc_attr( (string) get_custom_header()->height ); ?>" width="<?php echo esc_attr( (string) get_custom_header()->width ); ?>" alt="" />
								</div>
							</div>
							<?php endif; ?>

						</div>
						<!-- /gazou -->

					</header>
					<div id="content-w">
