<?php
$postfix = '';
$index   = '';
if ( isset( $ideapark_var['section_id'] ) ) {
	if ( preg_match( '~-(\d+)$~', $ideapark_var['section_id'], $match ) ) {
		$postfix = '_' . $match[1];
		$index   = '-' . absint( $match[1] );
	}
}
$layout        = ideapark_mod( 'home_banners_layout' . $postfix );
$section_style = [];
$section_class = [ 'l-section', 'c-home-banners', 'c-home-banners--' . $layout ];
if ( $layout != 1 || ideapark_mod( 'home_banners_1_container' . $postfix ) ) {
	$section_class[] = 'l-section--container';
}
if ( ideapark_mod( 'home_banners_top_margin' . $postfix ) ) {
	$section_class[] = 'c-home-banners--top-margin';
}
if ( $layout == 1 ) {
	if ( ideapark_mod( 'home_banners_1_text_align' . $postfix ) ) {
		$section_class[] = 'c-home-banners--' . ideapark_mod( 'home_banners_1_text_align' . $postfix );
	}
	$section_style[] = 'max-height:' . ideapark_mod( 'home_banners_1_height' . $postfix ) . 'px';
	$section_style[] = 'height:' . round( ideapark_mod( 'home_banners_1_height' . $postfix ) / 1170 * 100, 6 ) . 'vw';
}
?>

<section id="home-banners<?php echo esc_attr( $index ) ?>" class="<?php echo esc_attr( implode( ' ', $section_class ) ); ?>"<?php ideapark_style($section_style); ?>>
	<?php if ( $layout == 1 ) { ?>
		<?php ideapark_get_template_part( 'templates/home-banners-banner', [
			'banners'     => ideapark_mod( 'home_banners_1' . $postfix ),
			'type'        => $layout . '-1',
			'is_parallax' => ideapark_mod( 'home_banners_1_parallax' . $postfix ) && ! is_rtl()
		] ); ?>
	<?php } elseif ( $layout == 2 ) { ?>
		<div class="c-home-banners__wrap">
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_1' . $postfix ),
				'type'    => $layout . '-1'
			] ); ?>
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_2' . $postfix ),
				'type'    => $layout . '-2'
			] ); ?>
		</div>
	<?php } elseif ( $layout == 3 ) { ?>
		<div class="c-home-banners__wrap">
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_1' . $postfix ),
				'type'    => $layout . '-1'
			] ); ?>
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_2' . $postfix ),
				'type'    => $layout . '-2'
			] ); ?>
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_3' . $postfix ),
				'type'    => $layout . '-3'
			] ); ?>
		</div>
	<?php } elseif ( $layout == 4 ) { ?>
		<div class="c-home-banners__wrap">
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_1' . $postfix ),
				'type'    => $layout . '-1'
			] ); ?>
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_2' . $postfix ),
				'type'    => $layout . '-2'
			] ); ?>
		</div>
		<div class="c-home-banners__wrap">
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_3' . $postfix ),
				'type'    => $layout . '-3'
			] ); ?>
			<?php ideapark_get_template_part( 'templates/home-banners-banner', [
				'banners' => ideapark_mod( 'home_banners_4' . $postfix ),
				'type'    => $layout . '-4'
			] ); ?>
		</div>
	<?php } ?>
</section>


