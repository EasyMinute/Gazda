<article id="post-<?php the_ID(); ?>" <?php post_class( 'c-post-list' ); ?>>

	<?php $is_product = ( ideapark_woocommerce_on() && get_post_type() == 'product' ); ?>

	<?php if ( has_post_thumbnail() ) { ?>
		<div class="c-post-list__thumb">
			<a href="<?php echo get_permalink() ?>">
				<?php the_post_thumbnail( 'ideapark-post', [ 'class' => 'c-post-list__img' ] ); ?>
				<?php if ( is_sticky() ) { ?>
					<span class="c-post-list__sticky c-post-list__sticky--image"><?php echo ideapark_svg( 'stick', 'c-post-list__sticky-svg' ); ?></span>
				<?php } ?>
			</a>
		</div>
	<?php } else { ?>
		<?php if ( is_sticky() ) { ?>
			<span class="c-post-list__sticky c-post-list__sticky--no-image"><?php echo ideapark_svg( 'stick', 'c-post-list__sticky-svg' ); ?></span>
		<?php } ?>
	<?php } ?>

	<div class="c-post-list__meta">
		<?php if ( ! $is_product && get_post_type() != 'page' && ! ideapark_mod( 'post_hide_date' ) ) { ?>
			<span class="c-post-list__date">
				<?php the_time( get_option( 'date_format' ) ); ?>
			</span>
		<?php } ?>
		<?php if ( get_post_type() != 'page' && ! ideapark_mod( 'post_hide_category' ) ) { ?>
			<?php if ( $is_product ) {
				$product_categories = [];
				$term_ids = wc_get_product_term_ids( get_the_ID(), 'product_cat' );
				foreach ($term_ids AS $term_id) {
					$product_categories[] = get_term_by( 'id', $term_id, 'product_cat' );
				}
			} ?>
			<?php esc_html_e( 'In', 'foodz' ); ?>
			<ul class="c-post-list__categories">
				<li class="c-post-list__categories-item"><?php ideapark_category( ',</li> <li class="c-post-list__categories-item">', $is_product ? $product_categories : null, 'c-post-list__categories-item-link' ); ?></li>
			</ul>
		<?php } ?>
	</div>

	<h2 class="c-post-list__header"><a class="c-post-list__header-link" href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></h2>


	<div class="c-post-list__except">
		<?php if ( empty( $post->post_title ) ) { ?><a href="<?php echo get_permalink() ?>"><?php } ?>
			<?php the_excerpt() ?>
			<?php if ( empty( $post->post_title ) ) { ?></a><?php } ?>
	</div>

	<a class="c-post-list__continue" href="<?php echo get_permalink(); ?>"><?php if ( $is_product ) { ?><?php esc_html_e( 'Details', 'foodz' ); ?><?php } else { ?><?php esc_html_e( 'Continue Reading', 'foodz' ); ?><?php } ?></a>

</article>