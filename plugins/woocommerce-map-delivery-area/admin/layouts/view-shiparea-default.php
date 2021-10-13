<?php defined( 'ABSPATH' ) || exit; ?>

<tr valign="top" class="shiparea_admin_row">
	<th scope="row" class="titledesc shiparea_admin_cols"><?php esc_html_e( 'if the client is not inside any delivery area', 'shiparea' ); ?>:</th>
	<td class="forminp" class="shiparea_admin_cols">

		<fieldset>
			<label for="<?php echo $key_input; ?>_default_yes">
				<input type="radio" class="default_values" name="<?php echo $key_input; ?>[way]" id="<?php echo $key_input; ?>_default_yes" value="default_price" <?php checked($default_check, 'default_price'); ?> /> <?php esc_html_e('Use the default shipping price', 'shiparea'); ?>
			</label>
			<label for="<?php echo $key_input; ?>_default_no">
				<input type="radio" class="default_values" name="<?php echo $key_input; ?>[way]" id="<?php echo $key_input; ?>_default_no" value="avoid_purchase" <?php checked($default_check, 'avoid_purchase'); ?> /> <?php esc_html_e('Cancel purchase', 'shiparea'); ?>
			</label>
		</fieldset>

		<div class="shiparea_default_table_yes" style="<?php echo $default_css_yes; ?>">
			<table class="widefat wc_input_table" cellspacing="0">
				<thead>
					<tr>
						<th class="th_minprice cell_minprice" style="<?php echo $css_minprice; ?>"></th>
						<th colspan="2" class="center_minprice cell_minprice" style="<?php echo $css_minprice; ?>" >
							<?php esc_html_e( 'Minimum Price', 'shiparea' ); ?>
						</th>
						<th class="th_minprice cell_minprice" style="<?php echo $css_minprice; ?>"></th>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Label', 'shiparea' ); ?></th>
						
						<th class="cell_minprice" style="<?php echo $css_minprice; ?>"><?php esc_html_e( 'to Purchase', 'shiparea' ); ?></th>
						<th class="cell_minprice" style="<?php echo $css_minprice; ?>"><?php esc_html_e( 'to Free Delivery', 'shiparea' ); ?></th>

						<th><?php esc_html_e( 'Shipping Price', 'shiparea' ); ?></th>
					</tr>
				</thead>
				<tbody>	
					<?php
						$in_label		= wc_clean( $default_values['label'] );
						$in_shiprice	= wc_format_decimal( $default_values['shiprice'] );
						$in_minprice	= isset( $default_values['minprice'] ) ? wc_format_decimal( $default_values['minprice'] ) : 0;
						$in_free		= isset( $default_values['free'] ) ? wc_format_decimal( $default_values['free'] ) : 0;
					?>
					<tr>
						<td>
							<input type="text" name="<?php echo $key_input; ?>[values][label]" value="<?php echo $in_label; ?>" />
						</td>

						<td class="cell_minprice" style="<?php echo $css_minprice; ?>">
							<input type="number" step="0.01" name="<?php echo $key_input; ?>[values][minprice]" value="<?php echo $in_minprice; ?>" />
						</td>
						<td class="cell_minprice" style="<?php echo $css_minprice; ?>">
							<input type="number" step="0.01" name="<?php echo $key_input; ?>[values][free]" value="<?php echo $in_free; ?>" />
						</td>
						<td>
							<input type="number" step="0.01" name="<?php echo $key_input; ?>[values][shiprice]" value="<?php echo $in_shiprice; ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="shiparea_default_table_no" style="<?php echo $default_css_no; ?>">
			<table class="widefat wc_input_table" cellspacing="0">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Error Message', 'shiparea' ); ?></th>
					</tr>
				</thead>
				<tbody>	
					<?php $msg_error = wc_clean( $default_error_msg ); ?>
					<tr>
						<td>
							<input type="text" name="<?php echo $key_input; ?>[error_msg]" value="<?php echo $msg_error; ?>" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</td>
</tr>