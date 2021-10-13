<div class="shiparea_verify_address_container">
	
	<div class="shiparea_verify_address_form">
		<div class="shiparea_verify_address_row">
			<h2><?php _e('Verify your Address','letsgo'); ?></h2>
		</div>

		<div class="shiparea_verify_address_row">

			<div class="shiparea_verify_address_col1">
			
				<div class="shiparea_verify_address_section">
					<label for="shiparea_verify_address_select"><?php _e('Country/States : ','letsgo'); ?></label>
					<select id="shiparea_verify_address_select">
						<?php WC()->countries->country_dropdown_options($base_country,$base_state,true); ?>
					</select>
				</div>

				<div class="shiparea_verify_address_section">
					<label for="shiparea_verify_address_input"><?php _e('Address : ','letsgo'); ?></label>
					<input type="text" id="shiparea_verify_address_input" value="" />
				</div>

			</div>
		
			<div class="shiparea_verify_address_col2">

				<div id="shiparea_verify_address_result"></div>
				
			</div>
		</div>
	</div>
</div>