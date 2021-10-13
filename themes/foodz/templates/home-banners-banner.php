<?php if ( ! empty( $ideapark_var['banners'] ) && ! empty( $ideapark_var['type'] ) && ( $banners = array_keys( array_filter( ideapark_parse_checklist( $ideapark_var['banners'] ) ) ) ) ) {
	$banner_id   = 0;
	$banner_type = $ideapark_var['type'];

	$home_banners_shown_ids = ! empty( ideapark_mod( 'home_banners_shown_ids' ) ) ? ideapark_mod( 'home_banners_shown_ids' ) : [];
	if ( sizeof( $banners ) > 1 ) {
		if ( $home_banners_shown_ids ) {
			$banners = array_diff( $banners_old = $banners, $home_banners_shown_ids );
		}
		if ( ! $banners ) {
			$banners = $banners_old;
		}
		shuffle( $banners );
		$banner_id = array_pop( $banners );
	} else {
		$banner_id = array_pop( $banners );
	}
	if ( $banner_id ) {
		$home_banners_shown_ids[] = $banner_id;
		ideapark_mod_set_temp( 'home_banners_shown_ids', $home_banners_shown_ids );
	}
	if ( $banner_id && ( $post = get_post( $banner_id ) ) ) { ?>
		<?php
		/*
		 * @var $post WP_Post
		 * */
		$banner_hide_title  = get_post_meta( $post->ID, '_ip_banner_hide_title', true );
		$banner_title       = ! $banner_hide_title ? esc_html( $post->post_title ) : '';
		$banner_subheader   = esc_html( get_post_meta( $post->ID, '_ip_banner_subheader', true ) );
		$banner_button_text = esc_html( get_post_meta( $post->ID, '_ip_banner_button_text', true ) );
		$banner_button_link = preg_replace( '~^/~', home_url() . '/', get_post_meta( $post->ID, '_ip_banner_button_link', true ) );
		$banner_shortcode   = get_post_meta( $post->ID, '_ip_banner_shortcode', true );
		$banner_shortcode_p = get_post_meta( $post->ID, '_ip_banner_shortcode_placement', true );
		$b                  = get_post_meta( $post->ID, '_ip_banner_background_color', true );
		$banner_bg          = ideapark_mod_hex_color_norm( get_post_meta( $post->ID, '_ip_banner_background_color', true ), '' );
		$c                  = get_post_meta( $post->ID, '_ip_banner_color', true );
		$banner_color       = ideapark_mod_hex_color_norm( get_post_meta( $post->ID, '_ip_banner_color', true ), 'currentColor' );
		$attachment_id      = get_post_thumbnail_id( $post->ID );
		$image              = wp_get_attachment_image_src( $attachment_id, 'full' );
		$is_parallax        = ! empty( $ideapark_var['is_parallax'] ) && ! empty( $image ) && $banner_type == 1;
		$is_lazyload        = ideapark_mod( 'lazyload' ) && ! empty( $image );

		$banner_styles       = [];
		$banner_styles_image = [];
		if ( $banner_bg ) {
			$banner_styles[] = 'background-color:' . $banner_bg;
		}
		if ( $banner_color ) {
			$banner_styles[] = 'color:' . $banner_color;
		}
		if ( ! empty( $image ) && ! ideapark_mod( 'lazyload' ) ) {
			$banner_styles_image[] = 'background-image:url(\'' . esc_url( $image[0] ) . '\')';
		}
		?>
		<div
			class="c-home-banners__banner c-home-banners__banner--<?php echo esc_attr( $banner_type ); ?>" <?php ideapark_style( $banner_styles ); ?>>
			<div
				class="c-home-banners__banner-image c-home-banners__banner-image--<?php echo esc_attr( $banner_type ); ?><?php ideapark_class($is_parallax , 'c-home-banners__banner-image--parallax' ); ?><?php if ( $is_lazyload ) { ?> lazyload<?php } ?>" <?php ideapark_style( $banner_styles_image ); ?><?php if ( $is_lazyload ) { ?> data-bg="<?php echo esc_url( $image[0] ); ?>"<?php } ?>></div>

			<?php if ( $is_parallax ) { ?>
				<div class="c-home-banners__parallax">
					<img class="c-home-banners__parallax-img<?php if ( $is_lazyload ) { ?> parallax-lazy lazyload<?php } else {?> parallax<?php } ?>" <?php if ( ideapark_mod( 'lazyload' ) ) { ?>src="<?php echo ideapark_empty_gif(); ?>" data-<?php } ?>src="<?php echo esc_url( $image[0] ); ?>"
						 alt="<?php echo esc_attr( $banner_title ) ?>">
				</div>
			<?php } ?>

			<?php if ( $banner_type == '1-1' ) { ?>
			<div class="c-home-banners__inner-text">
				<?php } ?>

				<?php if ( $banner_shortcode && $banner_shortcode_p == 'above' ) {
					echo ideapark_wrap( ideapark_shortcode( $banner_shortcode ), '<div class="c-home-banners__code c-home-banners__code--above">', '</div>' );
				} ?>

				<?php echo ideapark_wrap( $banner_subheader, '<div class="c-home-banners__subheader">', '</div>' ); ?>
				<?php echo ideapark_wrap( $banner_title, '<div class="c-home-banners__title' . ( in_array( $banner_type, [
						'4-2',
						'4-3',
						'2-2'
					] ) ? ' c-home-banners__title-max' : '' ) . '">', '</div>' ); ?>

				<?php if ( $banner_button_link && $banner_button_text ) { ?>
					<div class="c-home-banners__button-wrap">
						<a class="c-home-banners__button"
						   href="<?php echo esc_url( $banner_button_link ); ?>"><?php echo esc_html( $banner_button_text ); ?></a>
					</div>
				<?php } ?>

				<?php if ( $banner_shortcode && $banner_shortcode_p == 'below' ) {
					echo ideapark_wrap( ideapark_shortcode( $banner_shortcode ), '<div class="c-home-banners__code c-home-banners__code--below">', '</div>' );
				} ?>

				<?php if ( $banner_type == '1-1' ) { ?>
			</div>
		<?php } ?>

			<?php if ( $banner_button_link && ! $banner_button_text ) { ?>
				<a class="c-home-banners__whole-link" href="<?php echo esc_url( $banner_button_link ); ?>"></a>
			<?php } ?>
		</div>
	<?php } elseif ( $banner_type != '1-1' ) { ?>
		<div
			class="c-home-banners__banner c-home-banners__banner--empty c-home-banners__banner--<?php echo esc_attr( $banner_type ); ?>"></div>
	<?php } ?>
<?php } ?>