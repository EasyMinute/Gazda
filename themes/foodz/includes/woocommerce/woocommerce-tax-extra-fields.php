<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Ideapark_Admin_Taxonomies {
	private $icon_ids = [];

	public function __construct() {
		add_action( 'wp_loaded', [ $this, 'init' ], 100 );
	}

	public function init() {
		ideapark_init_theme_mods();

		if ( ( $taxonomy = ideapark_mod( 'product_marker_attribute' ) ) && taxonomy_exists( $taxonomy ) ) {
			add_action( $taxonomy . '_add_form_fields', [ $this, 'add_taxonomy_fields' ], 100 );
			add_action( $taxonomy . '_edit_form_fields', [ $this, 'edit_taxonomy_fields' ], 100, 2 );
			add_action( 'created_term', [ $this, 'save_taxonomy_fields' ], 11, 3 );
			add_action( 'edit_term', [ $this, 'save_taxonomy_fields' ], 11, 3 );

		}
	}

	public function add_taxonomy_fields() {
		?>
		<div class="form-field term-thumbnail-wrap">
			<label><?php _e( 'Thumbnail', 'woocommerce' ); ?></label>
			<div id="product_taxonomy_term_thumbnail" class="ideapark-custom-tax__thumb">
				<img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" width="60px" height="60px" alt="thumb" /></div>
			<div class="ideapark-custom-tax__button">
				<input type="hidden" id="product_taxonomy_term_thumbnail_id" name="product_taxonomy_term_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'woocommerce' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'woocommerce' ); ?></button>
			</div>
			<script type="text/javascript">

				// Only show the "remove image" button when needed
				if (!jQuery('#product_taxonomy_term_thumbnail_id').val()) {
					jQuery('.remove_image_button').hide();
				}

				// Uploading files
				var file_frame;

				jQuery(document).on('click', '.upload_image_button', function (event) {

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if (file_frame) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title   : '<?php _e( 'Choose an image', 'woocommerce' ); ?>',
						button  : {
							text: '<?php _e( 'Use image', 'woocommerce' ); ?>'
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on('select', function () {
						var attachment = file_frame.state().get('selection').first().toJSON();
						var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

						jQuery('#product_taxonomy_term_thumbnail_id').val(attachment.id);
						jQuery('#product_taxonomy_term_thumbnail').find('img').attr('src', attachment_thumbnail.url);
						jQuery('.remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on('click', '.remove_image_button', function () {
					jQuery('#product_taxonomy_term_thumbnail').find('img').attr('src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>');
					jQuery('#product_taxonomy_term_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

				jQuery(document).ajaxComplete(function (event, request, options) {
					if (request && 4 === request.readyState && 200 === request.status
						&& options.data && 0 <= options.data.indexOf('action=add-tag')) {

						var res = wpAjax.parseAjaxResponse(request.responseXML, 'ajax-response');
						if (!res || res.errors) {
							return;
						}
						// Clear Thumbnail fields on submit
						jQuery('#product_taxonomy_term_thumbnail').find('img').attr('src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>');
						jQuery('#product_taxonomy_term_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						// Clear Display type field on submit
						jQuery('#display_type').val('');
						return;
					}
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	public function edit_taxonomy_fields( $term, $taxonomy = '' ) {
		$thumbnail_id = absint( get_term_meta( $term->term_id, 'ideapark_thumbnail_id', true ) );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = wc_placeholder_img_src();
		}
		?>
		<tr class="form-field term-thumbnail-wrap">
			<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'woocommerce' ); ?></label></th>
			<td>
				<div id="product_taxonomy_term_thumbnail" class="ideapark-custom-tax__thumb">
					<img src="<?php echo esc_url( $image ); ?>" width="60px" height="60px" /></div>
				<div class="ideapark-custom-tax__button">
					<input type="hidden" id="product_taxonomy_term_thumbnail_id" name="product_taxonomy_term_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
					<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'woocommerce' ); ?></button>
					<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'woocommerce' ); ?></button>
				</div>
				<script type="text/javascript">

					// Only show the "remove image" button when needed
					if ('0' === jQuery('#product_taxonomy_term_thumbnail_id').val()) {
						jQuery('.remove_image_button').hide();
					}

					// Uploading files
					var file_frame;

					jQuery(document).on('click', '.upload_image_button', function (event) {

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if (file_frame) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title   : '<?php _e( 'Choose an image', 'woocommerce' ); ?>',
							button  : {
								text: '<?php _e( 'Use image', 'woocommerce' ); ?>'
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on('select', function () {
							var attachment = file_frame.state().get('selection').first().toJSON();
							var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;

							jQuery('#product_taxonomy_term_thumbnail_id').val(attachment.id);
							jQuery('#product_taxonomy_term_thumbnail').find('img').attr('src', attachment_thumbnail.url);
							jQuery('.remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on('click', '.remove_image_button', function () {
						jQuery('#product_taxonomy_term_thumbnail').find('img').attr('src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>');
						jQuery('#product_taxonomy_term_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	public function save_taxonomy_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( ideapark_mod( 'product_marker_attribute' ) === $taxonomy ) {
			if ( isset( $_POST['product_taxonomy_term_thumbnail_id'] ) ) {
				update_term_meta( $term_id, 'ideapark_thumbnail_id', absint( $_POST['product_taxonomy_term_thumbnail_id'] ) );
			} else {
				delete_term_meta( $term_id, 'ideapark_thumbnail_id' );
			}
		}
	}

}

new Ideapark_Admin_Taxonomies();
