<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$nameError = "";
$selectorError = "";
$durationError = "";
$fadeError = "";
if(isset($_POST['submitted']) && !empty($_POST["submitted"])) {
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
	
	$centerx = intval($_POST['slideCenterx']);
	$centery = intval($_POST['slideCentery']);
	$status = intval($_POST['slideStatus']);	

	if(!isset($hasError)) {
		global $wpdb;
		$table_name_slides = $wpdb->prefix . "clean_slides_vava";		
		$wpdb->insert( 
			$table_name_slides, 
			array( 'id' => '', 'name' => $name, 'selector' => $selector, 'duration' => $duration, 'fade' => $fade, 'centeredx' => $centerx, 'centeredy' => $centery, 'status' => $status ) 
		);		
		echo '<p>New slideshow added, please wait...</p>';
		echo '<meta http-equiv="refresh" content="0;URL=\'admin.php?page=clean_slider\'" />';
		die();
	}
} ?>
	<style> .dashicons { opacity: .4; } span.error { color: #C00; } </style>
    <div class="form-wrap">
        <form action="" method="post" id="add_slider_form">
            <div class="form-field" style="min-width: 200px; width: 20%;">
                <label for="slideName">Name: 
                <span class="dashicons dashicons-info" title="The name is how it appears on your list."></span> 
                <?php if($nameError != "") { ?><span class="error"><?php echo $nameError;?></span><?php } ?></label>
                <input type="text" name="slideName" id="slideName" value="<?php if(isset($_POST['slideName'])) echo $_POST['slideName'];?>" required="required" autocomplete="off" maxlength="256" />
            </div>            
            <div class="form-field" style="min-width: 200px; width: 20%;">
                <label for="slideSelector">CSS ID: 
                <span class="dashicons dashicons-info" title="Unique CSS ID attribute shown on front-end."></span> 
                <?php if($selectorError != "") { ?><span class="error"><?php echo $selectorError;?></span><?php } ?></label>
                <input type="text" name="slideSelector" id="slideSelector" value="<?php if(isset($_POST['slideSelector'])) echo $_POST['slideSelector'];?>" required="required" autocomplete="off" maxlength="64" />
            </div>            
            <div class="form-field" style="display: inline-block; float: left; margin-right: 20px;">
                <label for="slideDuration">
                <?php if($durationError != "") { ?><span class="error"><?php echo $durationError;?></span><?php } ?>
                Duration: 
                <span class="dashicons dashicons-info" title="Amount of time in between slides (if slideshow)."></span> 
                </label>
                <input type="number" name="slideDuration" id="slideDuration" value="<?php if(isset($_POST['slideDuration'])) { echo $_POST['slideDuration']; } else { echo '5000'; }?>" required="required" min="3000" max="99999" step="100" maxlength="5" size="8" style="width: 120px;" />
            </div>            
            <div class="form-field" style=" min-width: 200px; width: 20%;">
                <label for="slideFade">
                <?php if($fadeError != "") { ?><span class="error"><?php echo $fadeError;?></span><?php } ?>
                Fade: 
                <span class="dashicons dashicons-info" title="Speed of fade transition between slides."></span>
                </label>
                <input type="number" name="slideFade" id="slideFade" value="<?php if(isset($_POST['slideFade'])) { echo $_POST['slideFade']; } else { echo '650'; }?>" required="required" min="350" max="1200" maxlength="4" step="10" size="8" style="width: 120px;" />
            </div>            
            <div class="form-field" style="display: inline-block; float: left; margin-right: 0px; width: 113px;">
                <label for="slideCenterx">X axis:
                <span class="dashicons dashicons-info" title="Should we center the image on the X axis?"></span>
                </label>
                <select name="slideCenterx" id="slideCenterx" class="postform">
                    <option value="1">No</option>
                    <option value="2" selected="selected">Yes</option>
                </select>
            </div>            
            <div class="form-field" style="min-width: 200px; width: 20%;">
                <label for="slideCentery">Y axis:
                <span class="dashicons dashicons-info" title="Should we center the image on the Y axis?"></span>
                </label>
                <select name="slideCentery" id="slideCentery" class="postform">
                    <option value="1">No</option>
                    <option value="2" selected="selected">Yes</option>
                </select>
            </div>            
            <div class="form-field" style="display: inline-block; float: left; margin-right: 20px; width: 94px;">
                <label for="status">Status:
                <span class="dashicons dashicons-info" title="Is slider activated (shown) or not?"></span>
                </label>
                <select name="slideStatus" id="slideStatus" class="postform">
                    <option value="2">Active</option>
                    <option value="1" selected="selected">Disabled</option>
                </select>
            </div>
            <div class="form-field" style="min-width: 200px; width: 20%;">
                <label style="padding-bottom: 5px;">&nbsp;</label>
                <input type="submit" value="Add new slider" class="button button-primary" />
            </div>
            <input type="hidden" name="submitted" id="submitted" value="true" />
            <?php 
            if(isset($hasError) || isset($captchaError)) { ?>
                <p class="error">Sorry, an error occured.</p>
            <?php } ?>
        </form>
    </div>
	<p style="clear: both; color: #f0f0f0; font-size: 8px; letter-spacing: 0.5em; opacity: 1; text-align:left; text-transform: uppercase;">made in : bih &middot; by : staging</p>