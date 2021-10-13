<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="shiparea_license"><?php _e('License Key', 'shiparea'); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
	</th>
	<td class="forminp forminp-license_key">
		<input
			name="shiparea_options[license_key]"
			id="shiparea_key_license"
			type="password"
			value="<?php echo esc_attr( $license_key ); ?>"
			class="regular-text"
			/>
			<button class="button button-primary" id="shiparea_button_verify_license">
				<?php _e('Verify Key','shiparea'); ?>
			</button>
			
			<?php if(  isset($license_key) && !empty($license_key) ) : ?>
				<button class="button button-secondary" id="shiparea_button_deactivate_license"><?php _e('Deactivate Key','shiprice'); ?></button>
			<?php endif; ?>

			<span id="shiparea_loading_license"></span>
			<p id="shiparea_message_license"></p>
	</td>
</tr>