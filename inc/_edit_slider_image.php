<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$slider_image_id = "";
$active_acc = '1';
$imageError = "";
wp_enqueue_media();
$slider_image_id = (int) $_GET['slide_image'];
if($slider_image_id == 0) { $hasError1 = true; } 
if(isset($_POST['changededimage']) && !empty($_POST["changededimage"])) {
	if(!isset($_POST['slideImage']) || trim($_POST['slideImage']) == '') { $slideImage = intval($_POST['slideImageOLD']); } 
	else { $slideImage = intval($_POST['slideImage']); }	
	if(!isset($_POST['slideimagedescription']) || trim($_POST['slideimagedescription']) == '') { $slideDesc = ''; } 
	else { $slideDesc = balanceTags(htmlspecialchars($_POST['slideimagedescription'], ENT_QUOTES), true); }	
	$sliderId = (int) $_POST['slideID'];
	$sliderImageId = (int) $_POST['slideImageID'];	
	$use_css = "0";
	if(isset($_POST['use_css'])) { $use_css = intval($_POST['use_css']); } 	
	if(!isset($hasError) || $sliderId || $sliderImageId > 0) {
		global $wpdb;
		$table_name_images = $wpdb->prefix . "clean_images_vava";		
		$wpdb->update( 
			$table_name_images, 
			array( 'description' => $slideDesc, 'image' => $slideImage, 'use_css' => $use_css  ), 
			array( 'id' => $sliderImageId, 'slider' => $sliderId ), array( '%s', '%d', '%d' ), 
			array( '%d', '%d' ) 
		);			
		echo '<h2>Slides &raquo; Slide image edited</h2>';
		echo '<p>Slideshow image successfully edited, please wait...</p>';
		echo '<meta http-equiv="refresh" content="0;URL=\'admin.php?page=clean_slider&vava='.$sliderId.'\'" />';		
		die();
	}
} 
	global $wpdb;
	$table_name_images = $wpdb->prefix . "clean_images_vava";
	$image = $wpdb->get_row( $wpdb->prepare( "SELECT slider, description, image, use_css FROM $table_name_images WHERE id = %d", $slider_image_id));	
	if(isset($hasError1)) { ?>
    	<p class="error">Sorry, an error occured. Please <a href="admin.php?page=clean_slider">go back</a> and try again.</p>
     <?php 
	 	die();
	 } ?>
<h2>Slides &raquo; <?php echo get_slide_name($image->slider); ?> <a href="admin.php?page=clean_slider&amp;vava=<?php echo $image->slider; ?>" class="add-new-h2">Go Back</a> <a href="admin.php?page=clean_slider&amp;action=add_slide" class="add-new-h2">Add New Slide</a></h2>
<style> .accordion-section-title span.dashicons { margin-right: 10px; } .wp-list-table thead th span.dashicons { font-size: 16px; padding-top: 3px; } .subsubsub { margin-bottom: 15px; } .accordion-section-content h2 { font-size: 1.2em; } .accordion-section-content h4 { margin-bottom: 0px; } .dashicons { opacity: .4; } span.error { color: #C00; } .sliderImagesListing { display: block; position: relative; } .sliderImagesListing li { display: block; padding: 20px 20px; position: relative; } .sliderImagesListing li:nth-child(even) { background: #FFF4FF; } .sliderImagesListing li:nth-child(odd) { background: #ECFFFF; } .sliderImagesListing li img { border: 2px solid #fff; display: block; float: left; margin-right: 20px; } .sliderImagesDesc { display: block; float: left; width: 60%; } a.sliderImagesEdit { position: absolute; right: 20px; top: 20px; } a.sliderImagesDelete { position: absolute; right: 20px; bottom: 20px; } </style>
<div class="accordion-container" style="border-top: 4px solid #DFDFDF; margin-top: 15px;">
	<div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-welcome-view-site"></span> View Old Image</h3>
	</div>
    <div class="accordion-section-content">
        <?php echo wp_get_attachment_image( $image->image, 'large' ); ?>
	</div>    
	<div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-edit"></span> Edit Slide Contents</h3>
	</div>
    <div class="accordion-section-content">
    	<div class="form-wrap">
            <form action="" method="post" id="edit_image_form">
                <div class="form-field" style="display: inline-block; float: left;">
                	<label>Image [JPG]: <span class="dashicons dashicons-info" title="For best results in full screen slideshows use image 1200px in width (JPG only)."></label>
        			<input type="text" class="media-input" name="slideImage" style="display: none;" />
        			<button class="media-button button" style="margin-top: 10px;">Choose or Upload new image</button> <?php if($imageError != "") { ?><span class="error" style="margin-left:20px; position: relative; top: 15px;"><?php echo $imageError;?></span><?php } ?><span class="error" id="upload_error" style="margin-left:20px; position: relative; top: 15px;"></span></p>
                </div>
                <div class="form-field" id="imagePreview" style="float:right; position: relative; z-index: 9999;"></div>
        		<div class="form-field" style="clear: both;">
                    <label for="slideImageDescription">Content (optional): <span class="dashicons dashicons-info" title="Here you can add image slide caption (HTML allowed)."></span></label>                    
                    <?php
					$content = htmlspecialchars_decode($image->description, ENT_QUOTES);
					$editor_id = 'slideimagedescription';
					$settings = array( 'media_buttons' => false, 'wpautop' => false, 'textarea_name' => 'slideimagedescription', 'drag_drop_upload' => false, 'editor_css' => '<style> textarea { resize: vertical; } </style>', 'editor_height' => '240' );
					wp_editor( $content, $editor_id, $settings );	 ?>       
                    <p style="padding-top: 10px">This area don't have any styling on front-end. If you put something there make sure that you have CSS style defined for used HTML elements.</p>    
                </div>                
                <div class="form-field">
                	<?php if($image->use_css == '1') { ?> <input type="checkbox" checked="checked" name="use_css" id="use_css1" value="1" class="postform" style="float: left; margin-right: 10px; margin-top: 3px;" /> <?php }
					else { ?> <input type="checkbox" name="use_css" value="1" class="postform" style="float: left; margin-right: 10px; margin-top: 3px;" /> <?php } ?>
                    <label for="use_css1">Use default style:
                    <span class="dashicons dashicons-info" title="If checked default CSS style will be applied, more info in documentation."></span>
                    </label>
                    <p style="padding-top: 10px">Default style is defined just for H1, H2, H3, P and A (link) elements.</p> 
                </div>
        		
                <div class="form-field">
                    <input type="hidden" name="slideID" value="<?php echo $image->slider; ?>" />
                    <input type="hidden" name="slideImageOLD" value="<?php echo $image->image; ?>" />
                    <input type="hidden" name="slideImageID" value="<?php echo $slider_image_id; ?>" />
                    <input type="submit" value="Save changes" class="button button-primary" />
                </div>
                <input type="hidden" name="changededimage" id="changededimage" value="true" />
                <br style="clear: both;" />
                <?php if(isset($hasError)) { ?>
                    <p class="error">Sorry, an error occured.</p>
                <?php } ?>
        	</form>
        </div>
    </div>	
</div>
<p style="color: #f0f0f0; font-size: 8px; letter-spacing: 0.5em; opacity: 1; text-align:center; text-transform: uppercase;">made in : bih &middot; by : staging</p>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$(".accordion-container").accordion({ heightStyle: "content", active: 1 });			
		$("#sliderAddImage").click(function(e){
			e.preventDefault();
			$( ".accordion-container" ).accordion( "option", "active", 2 );
		});			
	});		
	var gk_media_init = function(selector, button_selector)  {
		var clicked_button = false;	 
		jQuery(selector).each(function (i, input) {
			var button = jQuery(input).next(button_selector);
			button.click(function (event) {
				event.preventDefault();
				var selected_img;
				clicked_button = jQuery(this);
	 			if(wp.media.frames.gk_frame) { wp.media.frames.gk_frame.open(); return; }
				wp.media.frames.gk_frame = wp.media({ title: 'Select new image for slider', multiple: false, library: { type: 'image' }, button: { text: 'Use selected image' } });
	 			var gk_media_set_image = function() {
					var selection = wp.media.frames.gk_frame.state().get('selection');
	 				if (!selection) { return; }
	 				selection.each(function(attachment) {	
						if(attachment.attributes.subtype == 'jpeg') {
							var url = attachment.attributes.url;
							var aid = attachment.id;
							clicked_button.prev(selector).val(aid);				
							jQuery("#upload_error").text('');
							jQuery("#imagePreview").html('<label>Selected image:</label><div style="border: 1px solid #000; height: 75px; right: 0px; overflow: hidden; position: absolute; width:138px;"><img src="'+url+'" style="height: auto; width: 100%;" /></div>');
						} else { jQuery("#upload_error").text('Only JPG images allowed for slider.'); return; }						
					});
				};
				wp.media.frames.gk_frame.on('close', gk_media_set_image);
				wp.media.frames.gk_frame.on('select', gk_media_set_image);
				wp.media.frames.gk_frame.open();
			});
	   });
	};	
	gk_media_init('.media-input', '.media-button');		
</script>