<?php if ( $posts = get_posts( [
	'category'         => ideapark_mod( 'home_post_category' ),
	'posts_per_page'   => ideapark_mod( 'home_post_count' ),
	'meta_key'         => '_thumbnail_id',
	'suppress_filters' => false
] ) ) { ?>
	<section id="home-posts" class="l-section l-section--container c-posts">
		<?php if ( ideapark_mod( 'home_post_header' ) ) { ?>
			<div class="c-posts__header">
				<a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>">
				<?php echo esc_html( ideapark_mod( 'home_post_header' ) ) ?>
				</a>
			</div>
		<?php } ?>
		<div class="c-posts__list js-masonry">
			<?php foreach ( $posts as $i => $post ) {
				setup_postdata( $post );
				?>
				<div class="c-posts__item js-post-item">
					<?php get_template_part( 'content', 'list' ); ?>
				</div>
			<?php } ?>
			<div class="c-posts__sizer js-post-sizer"></div>
		</div>
	</section>
<?php } ?>