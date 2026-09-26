</div><!-- /contentw -->
<footer>
<div id="footer">
<div id="footer-in">
<?php // フッターメニュー
wp_nav_menu( array(
	'theme_location'  => 'secondary-menu',
	'container'       => 'div',
	'container_class' => 'footermenubox clearfix ',
	'menu_class'      => 'footermenust',
	'depth'           => 1,
) );
?>
	<div class="footer-wbox clearfix">

		<div class="footer-c">
			<!-- フッターのメインコンテンツ -->
			<p class="footerlogo">
			<!-- ロゴ又はブログ名 -->
				<?php if ( !is_home() || !is_front_page() ) { ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php } ?>
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
				<?php if ( !is_home() || !is_front_page() ) { ?>
					</a>
				<?php } ?>
			</p>

			<p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'description' ); ?></a>
			</p>
		</div>
	</div>

	<p class="copy">Copyright&copy;
		<?php bloginfo( 'name' ); ?>
		,
		<?php echo esc_html( wp_date( 'Y' ) ); ?>
		All Rights Reserved.</p>

</div>
</div>
</footer>
</div>
<!-- /#wrapperin -->
</div>
<!-- /#wrapper -->
</div><!-- /#st-ami -->
<!-- ページトップへ戻る -->
	<div id="page-top"><a href="#wrapper" class="fa fa-angle-up" aria-label="ページの先頭へ"></a></div>
<!-- ページトップへ戻る　終わり -->
<?php wp_footer(); ?>
</body></html>
