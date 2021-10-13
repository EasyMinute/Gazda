<div style="margin-bottom:10px;">
	<label><?php _e('Address', 'shiparea'); ?>:</label>
	<input id="areamaps_address" type="textbox" value="" size="35" />
	<input type="button" value="<?php _e('Search','shiparea'); ?>" onclick="codeAddress()" />

	<label style="margin-left:20px;"><?php _e('Line Color', 'shiparea'); ?>:</label>
	<input type="text" name="areamaps_lcolor" id="areamaps_lcolor" value="<?php echo $lcolor; ?>" />
</div>
        
<input type="hidden" name="areamaps_coords" id="areamaps_coords" value="" />
<input type="hidden" name="areamaps_lat" id="areamaps_lat" value="" />
<input type="hidden" name="areamaps_lng" id="areamaps_lng" value="" />
<input type="hidden" name="areamaps_zoom" id="areamaps_zoom" value="" />

<style>
	#main #content #areamaps_id img,
	#areamaps_id img { max-width: none; background-color: transparent; }
</style>
<div id="areamaps_id" style="width:100%; height:500px;"></div>