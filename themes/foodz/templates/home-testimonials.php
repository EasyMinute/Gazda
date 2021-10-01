<?php if ( $testimonials = get_posts( [
	'posts_per_page'   => - 1,
	'post_type'        => 'review',
	'suppress_filters' => false
] ) ) { ?>
	<section id="home-testimonials" class="l-section c-testimonials h-carousel <?php ideapark_class(ideapark_mod( 'home_testimonials_header' ) , 'c-testimonials--with-header' ); ?><?php ideapark_class( ideapark_mod( 'home_testimonials_margins'), 'c-testimonials--top-margin' ); ?><?php ideapark_class( ideapark_mod( 'home_testimonials_background_color'), 'c-testimonials--background' ); ?>" <?php echo ideapark_bg( ideapark_mod( 'home_testimonials_background_color' ) ); ?>>
		<div class="l-section__container c-testimonials__container">
			<?php if ( ideapark_mod( 'home_testimonials_header' ) ) { ?>
				<div class="c-testimonials__header"><?php echo esc_html( ideapark_mod( 'home_testimonials_header' ) ); ?></div>
			<?php } ?>
			<div class="c-testimonials__list js-testimonials-carousel">
				<?php foreach ( $testimonials as $i => $post ) {
					setup_postdata( $post );
					$testimonials_occupation = get_post_meta( $post->ID, '_ip_review_occupation', true );
					?>
					<div class="c-testimonials__item">
						<div class="c-testimonials__text">
							<?php the_excerpt(); ?>
						</div>
						<?php if ( has_post_thumbnail() ) { ?>
							<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); $image = $image[0]; ?>
							<div class="c-testimonials__thumb<?php if (ideapark_mod( 'lazyload' )) { ?> lazyload<?php } ?>" <?php echo ideapark_bg('', $image) ?>></div>
						<?php } ?>
						<div class="c-testimonials__author"><?php the_title(); ?></div>
						<?php if ( $testimonials_occupation ) { ?>
							<div class="c-testimonials__occupation">
								<?php echo esc_html( $testimonials_occupation ); ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</section>
<?php } ?>