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

$get_tab_title = function ( $tab, $postfix ) {
	$title = '';
	switch ( $tab ) {
		case 'featured_products':
			$title = ideapark_mod( 'home_featured_title' . $postfix );
			break;
		case 'sale_products':
			$title = ideapark_mod( 'home_sale_title' . $postfix );
			break;
		case 'best_selling_products':
			$title = ideapark_mod( 'home_best_selling_title' . $postfix );
			break;
		case 'recent_products':
			$title = ideapark_mod( 'home_recent_title' . $postfix );
			break;
		default:
			if ( ( $cat_id = absint( $tab ) ) && ( $cat = get_term_by( 'id', $cat_id, 'product_cat', 'ARRAY_A' ) ) ) {
				$title = $cat['name'];
			}
	}

	return $title;
}
?>
<?php if ( ideapark_woocommerce_on() && ideapark_mod( 'home_tab_products' . $postfix ) > 0 && ( $tabs = array_keys( array_filter( ideapark_parse_checklist( ideapark_mod( 'home_product_order' . $postfix ) ) ) ) ) ) { ?>
	<?php

	$is_first = true; ?>
	<div id="home-tabs<?php echo esc_attr( $index ) ?>" class="l-section c-home-tabs <?php ideapark_class( ideapark_mod( 'home_tab_top_margin' . $postfix ), 'c-home-tabs--top-margin' ); ?><?php ideapark_class( ideapark_mod( 'home_tab_carousel' . $postfix ), 'c-home-tabs--carousel h-carousel js-product-carousel' ); ?>">
		<div class="<?php if ( ! ideapark_mod( 'home_tab_fullscreen' . $postfix ) ) { ?>l-section__container <?php } else { ?>l-section__padding <?php } ?>c-home-tabs__header">
			<?php if ( sizeof( $tabs ) == 1 ) { ?>
				<div class="c-home-tabs__title">
					<?php echo esc_html( $get_tab_title( $tabs[0], $postfix ) ); ?>
				</div>
			<?php } else { ?>
				<ul class="c-home-tabs__header-list">
					<?php foreach ( $tabs as $tab ) { ?>
						<li class="c-home-tabs__header-item<?php if ( $is_first ) { ?> c-home-tabs__header-item--active h-wave<?php } ?>">
							<a class="c-home-tabs__header-link js-tab-title" href="#tab-<?php echo esc_attr( $tab . $index ); ?>">
								<?php echo esc_html( $get_tab_title( $tab, $postfix ) ); ?>
							</a>
						</li>
						<?php $is_first = false; ?>
					<?php } ?>
				</ul>
				<span class="c-home-tabs__header-select">
					<select class="c-home-tabs__header-custom-select js-tab-select">
						<?php foreach ( $tabs as $tab ) { ?>
							<option value="#tab-<?php echo esc_attr( $tab . $index ); ?>" <?php if ( $is_first ) { ?>selected<?php } ?>>
								<?php echo esc_html( $get_tab_title( $tab, $postfix ) ); ?>
							</option>
							<?php $is_first = false; ?>
						<?php } ?>
					</select>
				</span>
			<?php } ?>
		</div>
		<?php
			ideapark_mod_set_temp( 'product_grid_class', ideapark_mod( 'home_tab_carousel' . $postfix ) ? 'c-product-grid__list--carousel' : 'c-product-grid__list--center' );
		?>
		<?php $is_first = true; ?>
		<div class="c-home-tabs__wrap<?php ideapark_class( ideapark_mod( 'home_tab_fullscreen' . $postfix ), 'l-section__padding', 'l-section__container' ); ?>">
			<?php foreach ( $tabs as $tab ) { ?>
				<?php $cat_id = preg_match( '~^\d+$~', $tab ) ? $cat_id = absint( $tab ) : 0; ?>
				<?php ideapark_mod_set_temp( 'shortcode_fast_filter', ideapark_mod( 'home_tab_fast_filter' . $postfix ) ); ?>
				<div id="tab-<?php echo esc_attr( $tab . $index ); ?>" <?php if ( $cat_id ) { ?>data-index="<?php echo esc_attr( $index_id ); ?>" data-tab="<?php echo esc_attr( $tab ); ?>"<?php } ?> data-per-page="<?php echo esc_attr( ideapark_mod( 'home_tab_products' . $postfix ) ); ?>" <?php if ( ideapark_mod( 'home_tab_view_more' . $postfix ) && ideapark_mod( 'home_tab_view_more_item' . $postfix ) && ideapark_mod( 'home_tab_carousel' . $postfix ) && $cat_id ) { ?>data-view-more="<?php echo esc_url( get_term_link( $cat_id, 'product_cat' ) ); ?>"<?php } ?> class="c-home-tabs__tab<?php ideapark_class( $is_first, 'c-home-tabs__tab--active c-home-tabs__tab--visible' ); ?>">
					<?php if ( $cat_id ) { ?>
						<?php echo ideapark_shortcode( '[products category="' . $cat_id . '" limit="' . ideapark_mod( 'home_tab_products' . $postfix ) . '"' . ( ideapark_mod( 'home_tab_orderby' . $postfix ) ? ' orderby="' . ideapark_mod( 'home_tab_orderby' . $postfix ) . '" order="' . ideapark_mod( 'home_tab_order' . $postfix ) . '"' : '' ) . ']' ); ?>
					<?php } else { ?>
						<?php echo ideapark_shortcode( '[' . $tab . ' limit="' . ideapark_mod( 'home_tab_products' . $postfix ) . '"'. ( ideapark_mod( 'home_tab_orderby' . $postfix ) ? ' orderby="' . ideapark_mod( 'home_tab_orderby' . $postfix ) . '" order="' . ideapark_mod( 'home_tab_order' . $postfix ) . '"' : '' ) . ']' ); ?>
					<?php } ?>
					<?php if ( $cat_id && ideapark_mod( 'home_tab_view_more' . $postfix ) && ! ( ideapark_mod( 'home_tab_view_more_item' . $postfix ) && ideapark_mod( 'home_tab_carousel' . $postfix ) ) ) { ?>
						<div class="c-home-tabs__view-more-wrap">
							<a href="<?php echo esc_url( get_term_link( $cat_id, 'product_cat' ) ); ?>" class="c-home-tabs__view-more-button js-tab-view-more"><?php esc_html_e( 'View More', 'foodz' ); ?></a>
						</div>
					<?php } ?>
				</div>
				<?php ideapark_mod_set_temp( 'shortcode_fast_filter', null ); ?>
				<?php $is_first = false; ?>
			<?php } ?>
		</div>
		<?php
		ideapark_mod_set_temp( 'product_grid_class', null );
		?>
	</div>
<?php } ?>