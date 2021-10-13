<?php if ( ideapark_mod( 'slider_shortcode' ) ) { ?>
	<section id="home-slider" <?php echo ideapark_wrap( trim( 'l-section c-block js-home-slider' . ( ideapark_mod( "slider_fullwidth" ) ? '' : 'l-section--container' ) . ' ' . ( ideapark_mod( "slider_top_margin" ) ? 'c-block--margin' : '' ) ), 'class="', '"' ) ?>>
		<?php echo ideapark_shortcode( ideapark_mod( 'slider_shortcode' ) ); ?>
	</section>
<?php } ?>
