<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$nameError = "";
$selectorError = "";
$durationError = "";
$fadeError = "";
$slider_id = "";
$active_acc = '1';
$imageError = "";
$use_css = "0";
if((isset($_GET['active_acc']) ? $_GET['active_acc'] : null) == 'settings') { $active_acc = '0'; } 
else if((isset($_GET['active_acc']) ? $_GET['active_acc'] : null) == 'add') { $active_acc = '2'; }
wp_enqueue_media();
$slider_id = (int) $_GET['vava'];
if($slider_id == 0) { $hasError1 = true; } 
if(isset($_POST['submittedimage']) && !empty($_POST["submittedimage"])) {
	if(!isset($_POST['slideImage']) || trim($_POST['slideImage']) == '') {
		$imageError = 'Please select or upload image.';
		$hasError = true;
		$active_acc = '2';
	} else { $slideImage = intval($_POST['slideImage']); }	
	if(!isset($_POST['slideimagedescription']) || trim($_POST['slideimagedescription']) == '') { $slideDesc = ''; } 
	else { $slideDesc = balanceTags(htmlspecialchars($_POST['slideimagedescription'], ENT_QUOTES), true); }	
	$sliderId = (int) $_POST['slideID'];	
	$use_css = "0";
	if(isset($_POST['use_css'])) { $use_css = intval($_POST['use_css']); } 	
	if(!isset($hasError) && $sliderId > 0) {
		global $wpdb;
		$table_name_images = $wpdb->prefix . "clean_images_vava";		
		$wpdb->insert( 
			$table_name_images, 
			array( 'id' => '', 'slider' => $sliderId, 'description' => $slideDesc, 'image' => $slideImage, 'pos_ord' => '0', 'use_css' => $use_css ) 
		);
		echo '<h2>Slideshows &raquo; Slider image added</h2>';
		echo '<p>Slideshow image successfully added, please wait...</p>';
		echo '<meta http-equiv="refresh" content="0;URL=\'admin.php?page=clean_slider&vava='.$slider_id.'\'" />';		
		die();
	}
} 

elseif(isset($_POST['submitted']) && !empty($_POST["submitted"])) {
	if(!isset($_POST['slideName']) || trim($_POST['slideName']) == '') {
		$nameError = 'Please enter slider name.';
		$hasError = true;
	} else { $name = sanitize_text_field($_POST['slideName']); }	
	if(!isset($_POST['slideSelector']) || trim($_POST['slideSelector']) == '') {
		$selectorError = 'Please enter slider CSS ID attribute.';
		$hasError = true;
	} else { $selector = sanitize_title($_POST['slideSelector']); }	
	if(!isset($_POST['slideDuration']) || trim($_POST['slideDuration']) == '') {
		$durationError = '*';
		$hasError = true;
	} elseif(!is_numeric($_POST['slideDuration'])) {
		$durationError = '*';
		$hasError = true;
	} else { 
		$duration = intval($_POST['slideDuration']); 
		if ( strlen( $duration ) > 5 ) {
  			$durationError = '*';
			$hasError = true;
		} elseif($duration > 99999) {
			$durationError = '*';
			$hasError = true;
		} elseif($duration < 3000) {
			$durationError = '*';
			$hasError = true;
		}
	}
	
	if(!isset($_POST['slideFade']) || trim($_POST['slideFade']) == '') {
		$fadeError = '*';
		$hasError = true;
	} elseif(!is_numeric($_POST['slideFade'])) {
		$fadeError = '*';
		$hasError = true;
	} else { 
		$fade = intval($_POST['slideFade']); 
		if ( strlen( $fade ) > 4 ) {
  			$fadeError = '*';
			$hasError = true;
		} elseif($fade > 1200) {
			$fadeError = '*';
			$hasError = true;
		} elseif($fade < 350) {
			$fadeError = '*';
			$hasError = true;
		}
	}
	$centerx = (int) $_POST['slideCenterx'];
	$centery = (int) $_POST['slideCentery'];
	$status = (int) $_POST['slideStatus'];
	$slider_id = (int) $_POST['slideID'];
	if(!isset($hasError) && $centerx && $centery && $status && $slider_id > 0) {
		global $wpdb;
		$table_name_slides = $wpdb->prefix . "clean_slides_vava";		
		$wpdb->update( 
			$table_name_slides, 
			array( 'name' => $name, 'selector' => $selector, 'duration' => $duration, 'fade' => $fade, 'centeredx' => $centerx, 'centeredy' => $centery, 'status' => $status ), 
			array( 'id' => $slider_id ), array( '%s', '%s', '%d', '%d', '%d', '%d', '%d' ), 
			array( '%d' ) 
		);			
		echo '<h2>Slides</h2>';
		echo '<p>Slideshow successfully edited, please wait...</p>';
		echo '<meta http-equiv="refresh" content="0;URL=\'admin.php?page=clean_slider&vava='.$slider_id.'\'" />';		
		die();
	}

}
	global $wpdb;
	$table_name_slides = $wpdb->prefix . "clean_slides_vava";
	$slider = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, selector, duration, fade, centeredx, centeredy, status FROM $table_name_slides WHERE id = %d", $slider_id));	
	if(isset($hasError1)) { ?>
    	<p class="error">Sorry, an error occured. Please <a href="admin.php?page=clean_slider">go back</a> and try again.</p>
     <?php 
	 	die();
	 } ?>
<h2>Slides &raquo; <?php echo $slider->name; ?> <a href="admin.php?page=clean_slider&action=add_slide" class="add-new-h2">Add New Slide</a></h2>
<style> .accordion-section-title span.dashicons { margin-right: 10px; } .wp-list-table thead th span.dashicons { font-size: 16px; padding-top: 3px; } .subsubsub { margin-bottom: 15px; } .accordion-section-content h2 { font-size: 1.2em; } .accordion-section-content h4 { margin-bottom: 0px; } .dashicons { opacity: .4; } span.error { color: #C00; } .sliderImagesListing { display: block; position: relative; } .sliderImagesListing li { display: block; margin-bottom: 20px; padding: 0px; position: relative; } .sliderImagesListing li:nth-child(even) { background: #FFF4FF; } .sliderImagesListing li:nth-child(odd) { background: #ECFFFF; } .sliderImagesListing li img { border: 2px solid #ccc; display: block; float: left; margin-right: 20px; } .sliderImagesDesc { display: block; float: left; width: 56%; padding-bottom: 20px; padding-top: 20px; text-align:justify; } .sliderImagesDesc strong { border-bottom: 1px dashed #ccc; display: block; margin-bottom: 10px; padding-bottom: 7px; } .sliderImagesDesc br { display: none; } a.sliderImagesEdit { position: absolute; right: 20px; top: 20px; } a.sliderImagesDelete { position: absolute; right: 20px; bottom: 20px; } .sliderImagesDesc strong em { font-weight: normal; } a.sliderImagesDrag { cursor: move; position: absolute; right: 20px; top: 64px; } button.confirm { margin-bottom: 30px !important; } </style>
<div class="accordion-container" style="border-top: 4px solid #DFDFDF; margin-top: 15px;">
	<div class="accordion-section">
		<h3 class="accordion-section-title" style="color: #39F"><span class="dashicons dashicons-welcome-view-site" style="color: #06C"></span> Slider Settings</h3>
	</div>
    <div class="accordion-section-content">
        <div class="form-wrap">
            <form action="" method="post" id="edit_slider_form">
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label for="slideName">Name: 
                    <span class="dashicons dashicons-info" title="The name is how it appears on your list."></span> 
                    <?php if($nameError != "") { ?><span class="error"><?php echo $nameError;?></span><?php } ?></label>
                    <input type="text" name="slideName" id="slideName" value="<?php echo $slider->name; ?>" required="required" autocomplete="off" maxlength="256" />
                </div>                
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label for="slideSelector">CSS ID: 
                    <span class="dashicons dashicons-info" title="Unique CSS ID attribute shown on front-end"></span> 
                    <?php if($selectorError != "") { ?><span class="error"><?php echo $selectorError;?></span><?php } ?></label>
                    <input type="text" name="slideSelector" id="slideSelector" value="<?php echo $slider->selector; ?>" required="required" autocomplete="off" maxlength="64" />
                </div>                
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label for="slideDuration">
                    <?php if($durationError != "") { ?><span class="error"><?php echo $durationError;?></span><?php } ?>
                    Duration: 
                    <span class="dashicons dashicons-info" title="Amount of time in between slides (if slideshow)."></span> 
                    </label>
                    <input type="number" name="slideDuration" id="slideDuration" value="<?php echo $slider->duration; ?>" required="required" maxlength="5" size="8" min="3000" max="99999" step="100" style="width: 120px;" />
                </div>                
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label for="slideFade">
                    <?php if($fadeError != "") { ?><span class="error"><?php echo $fadeError;?></span><?php } ?>
                    Fade: 
                    <span class="dashicons dashicons-info" title="Speed of fade transition between slides."></span>
                    </label>
                    <input type="number" name="slideFade" id="slideFade" value="<?php echo $slider->fade; ?>" required="required" maxlength="4" min="350" max="1200" step="10" size="8" style="width: 120px;" />
                </div>                
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label for="slideCenterx">X axis:
                    <span class="dashicons dashicons-info" title="Should we center the image on the X axis?"></span>
                    </label>
                    <select name="slideCenterx" id="slideCenterx" class="postform">
                        <option value="1"<?php if($slider->centeredx == '1') { echo ' selected="selected"'; } ?>>No</option>
                        <option value="2"<?php if($slider->centeredx == '2') { echo ' selected="selected"'; } ?>>Yes</option>
                    </select>
                </div>                
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label for="slideCentery">Y axis:
                    <span class="dashicons dashicons-info" title="Should we center the image on the Y axis?"></span>
                    </label>
                    <select name="slideCentery" id="slideCentery" class="postform">
                        <option value="1"<?php if($slider->centeredy == '1') { echo ' selected="selected"'; } ?>>No</option>
                        <option value="2"<?php if($slider->centeredy == '2') { echo ' selected="selected"'; } ?>>Yes</option>
                    </select>
                </div>                
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px; width: 94px;">
                    <label for="status">Status:
                    <span class="dashicons dashicons-info" title="Is slider activated (shown) or not?"></span>
                    </label>
                    <select name="slideStatus" id="slideStatus" class="postform">
                        <option value="2"<?php if($slider->status == '2') { echo ' selected="selected"'; } ?>>Active</option>
                        <option value="1"<?php if($slider->status == '1') { echo ' selected="selected"'; } ?>>Disabled</option>
                    </select>
                </div>
                <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                    <label style="padding-bottom: 5px;">&nbsp;</label>
                    <input type="hidden" name="slideID" value="<?php echo $slider->id; ?>" />
                    <input type="submit" value="Save defaults" class="button button-primary" />
                </div>
                <input type="hidden" name="submitted" id="submitted" value="true" />
                <br style="clear: both;" />
                <?php 
                if(isset($hasError)) { ?>
                    <p class="error">Sorry, an error occured.</p>
                <?php } ?>
            </form>
        </div>
	</div>
    <div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-format-gallery"></span> Slider Images</h3>
	</div>
    <div class="accordion-section-content">
    	<?php
		$table_name_images = $wpdb->prefix . "clean_images_vava";
		$sliderImagesListing = $wpdb->get_results( $wpdb->prepare( "SELECT id, description, image, use_css FROM $table_name_images WHERE slider = %d ORDER BY pos_ord ASC", $slider_id ) );		
		if ( empty($sliderImagesListing) ){ echo '<p><em>There is no images yet... You can <a href="#" id="sliderAddImage">add images</a> to slider <strong>'.$slider->name.'</strong> now.</em></p>'; } 
		else {
			$i = 1;
			echo '<ul class="sliderImagesListing" id="set">';
			foreach ( $sliderImagesListing as $sliderImage ) {
				$sliderImg_id = $sliderImage->id;
				$sliderImg_desc = $sliderImage->description;
				$sliderImg_image = $sliderImage->image;
				$sliderImg_css = $sliderImage->use_css; ?>                
                <li id="sliderImage<?php echo $sliderImg_id; ?>" rel="<?php echo $sliderImg_id; ?>">
					<?php echo wp_get_attachment_image( $sliderImg_image, 'medium', array( 'class' => "attachment-small", 'alt' => trim(strip_tags( get_post_meta($sliderImg_image, '_wp_attachment_image_alt', true) )) )); ?>
                	<span class="sliderImagesDesc"><strong>Content (HTML code):
                    <?php if($sliderImg_css == '1') { echo "<em>with predefined style</em>"; }
					else { echo "<em>without predefined style</em>"; } ?>
                    </strong><br />
                    <?php echo $sliderImg_desc; ?>
                    </span>
                    <a href="#" class="sliderImagesDelete" rel="<?php echo $sliderImg_id; ?>" style="color: #930; text-decoration: none;"><span class="dashicons dashicons-dismiss" style="margin-top: -1px;"></span> Delete</a>
                    <a href="#" style="color: #ccc; text-decoration: none;" class="sliderImagesDrag"><span class="dashicons dashicons-image-flip-vertical" style="margin-top: -1px; float: right; padding-left: 7px;"></span> drag to sort</a>
                    <a href="admin.php?page=clean_slider&amp;action=edit_slide&amp;slide_image=<?php echo $sliderImg_id; ?>" class="sliderImagesEdit" style="text-decoration: none;"><span class="dashicons dashicons-admin-tools" style="margin-top: -1px;"></span> Change</a>
                    <br style="clear: both;" />                    
                </li>           
                <?php				
			}	
			echo '</ul>';		
		} 
		?>
    </div>
    <div class="accordion-section">
		<h3 class="accordion-section-title" style="color: #390"><span class="dashicons dashicons-plus-alt" style="color: #060"></span> Add New Image</h3>
	</div>
    <div class="accordion-section-content">
    	<div class="form-wrap">
            <form action="" method="post" id="add_image_form">
                <div class="form-field" style="display: inline-block; float: left;">
                	<label>Image [JPG]: <span class="dashicons dashicons-info" title="For best results in full screen slideshows use image 1200px in width (JPG only)."> </label>
        			<input type="number" class="media-input" name="slideImage" style="display: none;" />
        			<button class="media-button button" style="margin-top: 10px;">Choose or Upload image</button><?php if($imageError != "") { ?><span class="error" style="margin-left:20px; position: relative; top: 15px;"><?php echo $imageError;?></span><?php } ?> <span class="error" id="upload_error" style="margin-left:20px; position: relative; top: 15px;"></span></p>
                </div>
                <div class="form-field" id="imagePreview" style="float:right; position: relative; z-index: 9999;"></div>
        		<div class="form-field" style="clear: both;">
                    <label for="slideImageDescription">Content (optional): <span class="dashicons dashicons-info" title="Here you can add image slide caption (HTML allowed)."></span></label>                    
                    <?php
					$content = '';
					$editor_id = 'slideimagedescription';
					$settings = array( 'media_buttons' => false, 'wpautop' => false, 'textarea_name' => 'slideimagedescription', 'drag_drop_upload' => false, 'editor_css' => '<style> textarea { resize: vertical; } </style>', 'editor_height' => '240' );
					wp_editor( $content, $editor_id, $settings ); ?>       
                    <p style="padding-top: 10px">This area don't have any styling on front-end. If you put something there make sure that you have CSS style defined for used HTML elements.</p>    
                </div>                
                <div class="form-field">
                    <input type="checkbox" name="use_css" id="use_css" value="1" class="postform" style="float: left; margin-right: 10px; margin-top: 3px;" />
                    <label for="use_css">Use default style:
                    <span class="dashicons dashicons-info" title="If checked default CSS style will be applied, more info in documentation."></span>
                    </label>
                    <p style="padding-top: 10px">Default style is defined just for H1, H2, H3, P and A (link) elements.</p> 
                </div>        		
                <div class="form-field">
                    <input type="hidden" name="slideID" value="<?php echo $slider->id; ?>" />
                    <input type="submit" value="Add slide image" class="button button-primary" />
                </div>
                <input type="hidden" name="submittedimage" id="submittedimage" value="true" />
                <br style="clear: both;" />
                <?php 
                if(isset($hasError)) { ?>
                    <p class="error">Sorry, an error occured.</p>
                <?php } ?>
        	</form>
        </div>
    </div>    
</div>
<div class="postbox" style="margin-top: 20px; padding: 10px;">
	<p><strong>Shortcode:</strong><input type="text" value="[vava slider=&quot;<?php echo $slider->id; ?>&quot;]" onClick="this.setSelectionRange(0, this.value.length)" style="border: 0px; box-shadow: none; margin-top: 6px; position: relative; text-align:left;" /></p>
</div>
<p style="color: #f0f0f0; font-size: 8px; letter-spacing: 0.5em; opacity: 1; text-align:center; text-transform: uppercase;">made in : bih &middot; by : staging</p>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$(".accordion-container").accordion({ heightStyle: "content", active: <?php echo $active_acc; ?> });			
		$("#sliderAddImage").click(function(e){ e.preventDefault(); $( ".accordion-container" ).accordion( "option", "active", 2 ); });	
		$(".sliderImagesDrag").click(function(e){ e.preventDefault(); });	
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
				wp.media.frames.gk_frame = wp.media({
					title: 'Select image for slider',
					multiple: false,
					library: { type: 'image' },
					button: { text: 'Use selected image' }
				});
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