<?php
$postfix = '';
$index   = '';
if ( isset( $ideapark_var['section_id'] ) ) {
	if ( preg_match( '~-(\d+)$~', $ideapark_var['section_id'], $match ) ) {
		$postfix = '_' . $match[1];
		$index   = '-' . absint( $match[1] );
	}
}
?>
<?php if ( ideapark_mod( 'home_shortcode_content' . $postfix ) ) { ?>
	<section id="home-shortcode<?php echo esc_attr( $index ) ?>" <?php echo ideapark_wrap( trim( 'l-section c-block ' . ( ideapark_mod( 'home_shortcode_boxed' . $postfix ) ? 'l-section--container c-block--container' : 'c-block--fullwidth' ) . ' ' . ( ideapark_mod( 'home_shortcode_padding' . $postfix ) ? 'c-block--padding' : '' ) . ' ' . ( ideapark_mod( "home_shortcode_margins" . $postfix ) ? 'c-block--margin' : '' ) . ' ' . ( ideapark_mod( "home_shortcode_header" . $postfix ) ? 'c-block--header' : '' ) ), 'class="', '"' ) ?> <?php echo ideapark_bg( ideapark_mod( 'home_shortcode_background_color' . $postfix ) ); ?>>
		<?php if ( ideapark_mod( 'home_shortcode_container' . $postfix ) && ! ideapark_mod( 'home_shortcode_boxed' . $postfix ) ) { ?>
		<div class="l-section__container"><?php } ?>
			<?php if ( ideapark_mod( 'home_shortcode_header' . $postfix ) ) { ?>
				<div class="c-block__header">
					<?php echo esc_html( ideapark_mod( 'home_shortcode_header' . $postfix ) ) ?>
				</div>
			<?php } ?>

			<?php echo ideapark_shortcode( ideapark_mod( 'home_shortcode_content' . $postfix ) ); ?>
			<?php if ( ideapark_mod( 'home_shortcode_container' . $postfix ) && ! ideapark_mod( 'home_shortcode_boxed' . $postfix ) ) { ?>
		</div><?php } ?>
	</section>
<?php } ?>