<?php
$postfix  = '';
$index    = '';
$index_id = '';
if ( isset( $ideapark_var['section_id'] ) ) {
	if ( preg_match( '~-(\d+)$~', $ideapark_var['section_id'], $match ) ) {
		$index_id = absint( $match[1] );
		$postfix  = '_' . $index_id;
		$index    = '-' . $index_id;
	}
}
$section_style = [];
$tab           = ideapark_mod( 'home_promo_source' . $postfix );
$content_type  = ideapark_mod( 'home_promo_content_type' . $postfix );
ideapark_mod_set_temp( 'product_grid_class', 'c-product-grid__list--carousel-short' );
?>
<?php if ( ideapark_woocommerce_on() && ideapark_mod( 'home_promo_products' . $postfix ) > 0 ) { ?>
	<div id="home-promo<?php echo esc_attr( $index ) ?>" class="l-section c-home-promo <?php ideapark_class( ideapark_mod( 'home_promo_top_margin' . $postfix ), 'c-home-promo--top-margin' ); ?>">
		<div class="l-section__container c-home-promo__wrap">
			<div class="c-home-promo__col-content c-home-promo__col-content--<?php echo ideapark_mod( 'home_promo_content_type' . $postfix ); ?> c-home-promo__col-content--<?php echo ideapark_mod( 'home_promo_layout' ); ?> <?php ideapark_class( $content_type == 'html', 'entry-content' ); ?> <?php if ( ideapark_mod( 'lazyload' ) && ideapark_mod( 'home_promo_background_image' . $postfix ) ) { ?>lazyload<?php } ?>" <?php echo ideapark_bg( ideapark_mod_hex_color_norm( 'home_promo_background_color' ), ideapark_mod( 'home_promo_background_image' . $postfix ) ) ?>>
				<?php echo ideapark_shortcode( ideapark_mod( ( $content_type == 'html' ? 'home_promo_content' : 'home_promo_shortcode' ) . $postfix ) ); ?>
			</div>
			<div class="c-home-promo__col-products c-home-promo__col-products--<?php echo ideapark_mod( 'home_promo_layout' ); ?>">
				<div class="c-home-promo__title">
					<?php echo esc_html( ideapark_mod( 'home_promo_title' . $postfix ) ); ?>
				</div>
				<?php $cat_id = preg_match( '~^\d+$~', $tab ) ? $cat_id = absint( $tab ) : 0; ?>
				<div <?php if ( $cat_id ) { ?>data-index="<?php echo esc_attr( $index_id ); ?>" data-tab="<?php echo esc_attr( $tab ); ?>"<?php } ?> data-per-page="<?php echo esc_attr( ideapark_mod( 'home_promo_products' . $postfix ) ); ?>" <?php if ( ideapark_mod( 'home_promo_view_more' . $postfix ) && $cat_id ) { ?>data-view-more="<?php echo esc_url( get_term_link( $cat_id, 'product_cat' ) ); ?>"<?php } ?> class="c-home-promo__products js-home-promo-carousel">
					<?php if ( $cat_id ) { ?>
						<?php echo ideapark_shortcode( '[products category="' . $cat_id . '" limit="' . ideapark_mod( 'home_promo_products' . $postfix ) . '"' . ( ideapark_mod( 'home_promo_orderby' . $postfix ) ? ' orderby="' . ideapark_mod( 'home_promo_orderby' . $postfix ) . '" order="' . ideapark_mod( 'home_promo_order' . $postfix ) . '"' : '' ) . ']' ); ?>
					<?php } else { ?>
						<?php echo ideapark_shortcode( '[' . $tab . ' limit="' . ideapark_mod( 'home_promo_products' . $postfix ) . '"' . ( ideapark_mod( 'home_promo_orderby' . $postfix ) ? ' orderby="' . ideapark_mod( 'home_promo_orderby' . $postfix ) . '" order="' . ideapark_mod( 'home_promo_order' . $postfix ) . '"' : '' ) . ']' ); ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php }
ideapark_mod_set_temp( 'product_grid_class', null );
?>