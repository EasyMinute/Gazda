<?php wp_reset_postdata(); ?>
<?php if ( ( $post = get_post() ) && ( $content = apply_filters( 'the_content', $post->post_content ) ) ) { ?>
	<section id="home-text" <?php echo ideapark_wrap( trim( 'l-section c-block c-block--padding ' . ( ideapark_mod( 'home_text_boxed' ) ? 'l-section--container c-block--container' : '' ) . ' ' . ( ideapark_mod( "home_text_margins" ) ? 'c-block--margin' : '' ) ), 'class="', '"' ) ?><?php echo ideapark_bg( ideapark_mod( 'home_text_background_color' ) ); ?>>
		<?php if ( ! ideapark_mod( 'home_text_boxed' ) ) { ?>
		<div class="l-section__container"><?php } ?>
			<?php if ( ! ideapark_mod( 'home_text_hide_header' ) ) { ?>
				<h1 class="c-block__header"><?php echo get_the_title( $post ); ?></h1>
			<?php } ?>
			<?php echo ideapark_wrap( $content, '<div class="entry-content c-block__content">', '</div>' ); ?>
			<?php if ( ! ideapark_mod( 'home_text_boxed' ) ) { ?>
		</div><?php } ?>
	</section>
<?php } ?>