<?php global $post; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'c-post c-post__content' ); ?>>
	<div class="entry-content <?php ideapark_class( ideapark_mod( 'sidebar_post' ), 'entry-content--sidebar', 'entry-content--fullwidth' ); ?>">
		<?php if ( has_post_thumbnail() && ! ideapark_mod( 'post_hide_featured_image' ) ) { ?>
			<figure class="wp-block-image alignwide c-post__thumb">
				<?php the_post_thumbnail( 'full', [ 'class' => 'c-post__thumb-img' ] ); ?>
			</figure>
		<?php } ?>
		<?php the_content( '<span class="c-post__more-button">' . esc_html__( 'Continue Reading', 'foodz' )  . '</span>' ); ?>
	</div>

	<?php wp_link_pages( [
		'before'   => '<div class="c-post__page-links"><div class="c-post__page-links-title">' . esc_html__( 'Pages:', 'foodz' ) . '</div>',
		'after'    => '</div>',
		'pagelink' => '<span>%</span>'
	] ); ?>

	<?php if ( ! is_page() && ( ! ideapark_mod( 'post_hide_share' ) || ! ideapark_mod( 'post_hide_tags' ) ) ) { ?>
		<div class="c-post__bottom">
			<?php if ( has_tag() && ! ideapark_mod( 'post_hide_tags' ) ) { ?>
				<div class="c-post__tags">
					<?php the_tags( "", "" ); ?>
				</div>
			<?php } ?>
			<?php if ( ! ideapark_mod( 'post_hide_share' ) && shortcode_exists( 'ip-post-share' ) ) { ?>
				<div class="c-post__share">
					<?php echo ideapark_shortcode( '[ip-post-share]' ); ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ( is_single() && ! ideapark_mod( 'post_hide_author' ) ) { ?>
		<?php get_template_part( 'templates/post-author' ); ?>
	<?php } ?>

	<?php if ( is_single() && ! ideapark_mod( 'post_hide_postnav' ) ) { ?>
		<?php ideapark_post_nav(); ?>
	<?php } ?>

	<?php comments_template( '', true ); ?>

</article>

