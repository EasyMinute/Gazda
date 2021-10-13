<?php defined( 'ABSPATH' ) || exit; ?>

<tr valign="top" class="shiparea_admin_row">
	<th scope="row" class="titledesc shiparea_admin_cols"><?php esc_html_e( 'Areas', 'shiparea' ); ?>:</th>
	<td class="forminp" class="shiparea_admin_cols">
		<table id="shiparea_admin_table" class="widefat wc_input_table sortable" cellspacing="0">
			<thead>
				<tr>
					<th class="sort th_minprice cell_minprice" style="<?php echo $css_minprice; ?>"></th>
					<th class="th_minprice cell_minprice" style="<?php echo $css_minprice; ?>"></th>
					<th class="th_minprice cell_minprice" style="<?php echo $css_minprice; ?>"></th>
					<th colspan="2" class="center_minprice cell_minprice" style="<?php echo $css_minprice; ?>" >
						<?php esc_html_e( 'Minimum Price', 'shiparea' ); ?>
					</th>
					<th class="th_minprice cell_minprice" style="<?php echo $css_minprice; ?>"></th>
				</tr>
				<tr>
					<th class="sort">&nbsp;</th>
					<th><?php esc_html_e( 'AreaMaps', 'shiparea' ); ?></th>
					<th><?php esc_html_e( 'Label', 'shiparea' ); ?></th>
					
					<th class="cell_minprice" style="<?php echo $css_minprice; ?>"><?php esc_html_e( 'to Purchase', 'shiparea' ); ?></th>
					<th class="cell_minprice" style="<?php echo $css_minprice; ?>"><?php esc_html_e( 'to Free Delivery', 'shiparea' ); ?></th>

					<th><?php esc_html_e( 'Shipping Price', 'shiparea' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if( count($areas) > 0 ) : $i = 0; ?>
				<?php foreach($areas as $area) : ?>
					<?php
						$in_areamaps	= wc_clean($area['areamaps']);
						$in_label		= wc_clean( $area['label'] );
						$in_shiprice	= wc_format_decimal( $area['shiprice'] );
						$in_minprice	= isset( $area['minprice'] ) ? wc_format_decimal( $area['minprice'] ) : 0;
						$in_free		= isset( $area['free'] ) ? wc_format_decimal( $area['free'] ) : 0;
					?>
					<tr id="trarea_<?php echo $i; ?>">
						<td class="sort">&nbsp;</td>
						<td>
							<?php if( count($areamaps) > 0 ) : ?>
								<select name="<?php echo $key_input; ?>[areas][trarea_<?php echo $i; ?>][areamaps][]" multiple="multiple" class="areamaps chosen_select">
									<?php foreach($areamaps as $areamap) : ?>
										<option value="<?php echo $areamap['id']; ?>" <?php echo wc_selected($areamap['id'], $in_areamaps); ?>>
											<?php echo $areamap['text']; ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php else: ?>
								<?php esc_html_e('There is not any available area','shiparea'); ?>
							<?php endif; ?>
						</td>

						<td>
							<input type="text" name="<?php echo $key_input; ?>[areas][trarea_<?php echo $i; ?>][label]" value="<?php echo $in_label; ?>" />
						</td>

						<td class="cell_minprice" style="<?php echo $css_minprice; ?>">
							<input type="number" step="0.01" name="<?php echo $key_input; ?>[areas][trarea_<?php echo $i; ?>][minprice]" value="<?php echo $in_minprice; ?>" />
						</td>
						<td class="cell_minprice" style="<?php echo $css_minprice; ?>">
							<input type="number" step="0.01" name="<?php echo $key_input; ?>[areas][trarea_<?php echo $i; ?>][free]" value="<?php echo $in_free; ?>" />
						</td>
						<td>
							<input type="number" step="0.01" name="<?php echo $key_input; ?>[areas][trarea_<?php echo $i; ?>][shiprice]" value="<?php echo $in_shiprice; ?>" />
						</td>
					</tr>
					<?php $i++; ?>

				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="7">
						<a href="#" class="add button">
							<?php esc_html_e( '+ Add Shipping Area', 'shiparea' ); ?>
						</a>
						&nbsp;
						<a href="#" class="remove_rows button">
							<?php esc_html_e( 'Remove selected areas(s)', 'shiparea' ); ?>
						</a>
						&nbsp;
						<a href="#" class="remove_all button">
							<?php esc_html_e( 'Remove all areas', 'shiparea' ); ?>
						</a>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
</tr>