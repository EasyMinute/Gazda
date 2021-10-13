<?php
global $ideapark_customize;

$description = get_the_author_meta( 'description' );
$author_soc  = "";

if ( ! empty( $ideapark_customize ) ) {
	ob_start();
	foreach ( $ideapark_customize AS $section ) {
		if ( ! empty( $section['controls'] ) && array_key_exists( 'facebook', $section['controls'] ) ) {
			foreach ( $section['controls'] AS $control_name => $control ) { ?>
				<?php if ( strpos($control_name, 'soc_') === false && get_the_author_meta( $control_name ) ) { ?>
					<a href="<?php echo esc_url( get_the_author_meta( $control_name ) ); ?>">
						<svg class="soc-img">
							<use xlink:href="<?php echo esc_url( ideapark_svg_url() ); ?>#svg-<?php echo esc_attr( $control_name ); ?>" />
						</svg>
					</a>
				<?php } ?>
			<?php }
		}
	}
	$author_soc = trim( ob_get_clean() );
}
?>

<?php if ( $description || $author_soc ) { ?>
	<div class="post-author clearfix">

		<div class="author-img">
			<?php echo get_avatar( get_the_author_meta( 'email' ) ); ?>
		</div>

		<div class="author-content">
			<h5><?php the_author_posts_link(); ?></h5>

			<?php echo ideapark_wrap( $description, '<p>', '</p>' ); ?>
			<?php echo ideapark_wrap( $author_soc, '<div class="soc">', '</div>' ); ?>

		</div>

	</div>
<?php } ?>