<?php if ( $brands = get_posts( [
	'posts_per_page'   => - 1,
	'post_type'        => 'brand',
	'meta_key'         => '_thumbnail_id',
	'suppress_filters' => false
] ) ) { ?>
	<section id="home-brands" class="l-section c-brands h-carousel">
		<div class="l-section__container c-brands__container">
			<?php if ( ideapark_mod( 'home_brands_header' ) ) { ?>
				<div class="c-brands__header"><?php echo esc_html( ideapark_mod( 'home_brands_header' ) ); ?></div>
			<?php } ?>
			<div class="c-brands__list js-brands-carousel">
				<?php foreach ( $brands as $post ) {
					setup_postdata( $post );
					$brand_link    = get_post_meta( $post->ID, '_ip_brand_link', true );
					$attachment_id = get_post_thumbnail_id( $post->ID );
					$image         = wp_get_attachment_image_src( $attachment_id, 'ideapark-home-brands' );
					$image_srcset  = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'ideapark-home-brands' ) : false;
					$image_alt     = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
					if ( empty( $image_alt ) ) {
						$image_alt = get_the_title();
					}
					?>

					<div class="c-brands__item">
						<?php if ( ideapark_mod( 'lazyload' ) ) { ?>
							<img src="<?php echo ideapark_empty_gif(); ?>" class="lazyload c-brands__img" <?php if ( $image_srcset ) { ?>data-srcset="<?php echo esc_attr( $image_srcset ); ?>" <?php } else { ?>data-src="<?php echo esc_url( $image[0] ); ?>" <?php } ?> alt="<?php echo esc_attr( $image_alt ); ?>" />
						<?php } else { ?>
							<img class="c-brands__img" <?php if ( $image_srcset ) { ?>srcset="<?php echo esc_attr( $image_srcset ); ?>" <?php } else { ?>src="<?php echo esc_url( $image[0] ); ?>" <?php } ?> alt="<?php echo esc_attr( $image_alt ); ?>" />
						<?php } ?>
						<?php if ( $brand_link ) { ?>
						<a class="c-brands__link" href="<?php echo esc_url( $brand_link ); ?>" title="<?php echo esc_attr( $image_alt ); ?>"></a>
						<?php }  ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
<?php } ?>