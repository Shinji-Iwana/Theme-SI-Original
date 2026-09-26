<?php
/**
 * トップページ用のSNSボタン
 *
 * STINGER8 の Google+（2019年終了）と Pocket（2025年終了）のボタンは外した。
 */
$url_encode   = rawurlencode( home_url( '/' ) );
$title_encode = rawurlencode( get_bloginfo( 'name' ) );
?>

<div class="sns">
	<ul class="clearfix">
		<!--ツイートボタン-->
		<li class="twitter">
		<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $url_encode ); ?>&amp;text=<?php echo esc_attr( $title_encode ); ?>" target="_blank" rel="noopener"><i class="fa fa-twitter"></i><span class="snstext">Twitter</span></a>
		</li>

		<!--Facebookボタン-->
		<li class="facebook">
		<a href="https://www.facebook.com/sharer.php?src=bm&amp;u=<?php echo esc_attr( $url_encode ); ?>&amp;t=<?php echo esc_attr( $title_encode ); ?>" target="_blank" rel="noopener"><i class="fa fa-facebook"></i><span class="snstext">Facebook</span></a>
		</li>

		<!--はてブボタン-->
		<li class="hatebu">
			<a href="https://b.hatena.ne.jp/entry/<?php echo esc_attr( home_url( '/' ) ); ?>" class="hatena-bookmark-button" data-hatena-bookmark-layout="simple" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"><span style="font-weight:bold" class="fa-hatena">B!</span><span class="snstext">はてブ</span></a><script src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8" async="async"></script>
		</li>

		<!--LINEボタン-->
		<li class="line">
			<a href="https://line.me/R/msg/text/?<?php echo esc_attr( $title_encode . '%0A' . $url_encode ); ?>" target="_blank" rel="noopener"><i class="fa fa-comment" aria-hidden="true"></i><span class="snstext">LINE</span></a>
		</li>

	</ul>
</div>
