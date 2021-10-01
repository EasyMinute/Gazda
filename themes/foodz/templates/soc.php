<?php
global $ideapark_customize;
$soc_list  = [];
$soc_count = 0;

if ( ! empty( $ideapark_customize ) ) {
	foreach ( $ideapark_customize as $section ) {
		if ( ! empty( $section['controls'] ) && array_key_exists( 'facebook', $section['controls'] ) ) {
			foreach ( $section['controls'] as $key => $control ) {
				if ( ! empty( $control['type'] ) && $control['type'] == 'text' ) {
					$soc_list[] = $key;
				}
			}
			break;
		}
	}
}
?>
<?php if ( $soc_list ) { ?>
	<div
		class="c-soc <?php if ( ! empty( $ideapark_var['class'] ) ) { ?><?php echo esc_attr( $ideapark_var['class'] ); ?><?php } ?>">
		<?php
		foreach ( $soc_list as $soc_name ) {
			if ( ! preg_match( '~^custom~', $soc_name ) && ideapark_mod( $soc_name ) ): $soc_count ++; ?>
				<a href="<?php echo esc_url( ideapark_mod( $soc_name ) ); ?>"><?php echo ideapark_svg( $soc_name, 'c-soc__svg c-soc__svg--' . esc_attr( $soc_name ) ); ?></a>
			<?php endif;
		} ?>

		<?php if (
			ideapark_mod( 'custom_soc_icon' ) &&
			ideapark_mod( 'custom_soc_url' ) &&
			! empty( ideapark_mod( 'custom_soc_icon__attachment_id' ) ) &&
			( $attachment_id = ideapark_mod( 'custom_soc_icon__attachment_id' ) ) &&
			( $type = get_post_mime_type( $attachment_id ) )
		) {
			$soc_count ++; ?>
			<a href="<?php echo esc_url( ideapark_mod( 'custom_soc_url' ) ); ?>" target="_blank"
			   <?php if ( ideapark_mod( 'soc_background_color' ) && ideapark_mod( 'soc_background_color' ) != ideapark_mod_default( 'soc_background_color' ) ) { ?>style="background-color: <?php echo esc_attr( ideapark_mod( 'soc_background_color' ) ); ?>"<?php } ?>>

				<?php
				if ( $type == 'image/svg+xml' ) {
					echo ideapark_get_inline_svg( $attachment_id, 'c-soc__svg c-soc__svg--custom' );
				} else {
					$image     = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
					$image_alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
					if ( empty( $image_alt ) ) {
						$image_alt = get_the_title( $attachment_id );
					}
					if ( ideapark_mod( 'lazyload' ) ) {
						echo '<img src="' . ideapark_empty_gif() . '" data-src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( $image_alt ) . '" class="c-soc__svg c-soc__svg--custom c-soc__svg--image lazyload" />';
					} else {
						echo '<img src="' . esc_url( $image[0] ) . '" alt="' . esc_attr( $image_alt ) . '" class="c-soc__svg c-soc__svg--custom c-soc__svg--image" />';
					}
					?>
				<?php } ?>
			</a>

		<?php } ?>
	</div>
<?php } ?>