<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function countImages($from){
	global $wpdb;
	$slider = intval($from);
	$table_name_slides = $wpdb->prefix . "clean_images_vava";	
	$counted = $wpdb->get_var( $wpdb->prepare(" SELECT COUNT(id) FROM $table_name_slides WHERE slider = %d", $slider ));	
	if($counted == '0') { return ''; } 
	else { return $counted; }
}

function countStatus($wich){
	global $wpdb;
	$status = intval($wich);
	$table_name_slides = $wpdb->prefix . "clean_slides_vava";
	$counted = $wpdb->get_var( $wpdb->prepare(" SELECT COUNT(id) FROM $table_name_slides WHERE status = %d", $status ));	
	return $counted; 
}

function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);   
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

function plugin_get_description() {	
	require_once( ABSPATH . 'wp-content/plugins/clean-slider/inc/Parsedown.php' );
	$contents = file_get_contents(ABSPATH . 'wp-content/plugins/clean-slider/readme.txt', FILE_USE_INCLUDE_PATH);	
	$content = get_string_between($contents,'== Description ==','== Installation ==');
	$Parsedown = new Parsedown();
	return $Parsedown->text($content);
}

function plugin_get_help() {	
	require_once( ABSPATH . 'wp-content/plugins/clean-slider/inc/Parsedown.php' );
	$contents = file_get_contents(ABSPATH . 'wp-content/plugins/clean-slider/readme.txt', FILE_USE_INCLUDE_PATH);	
	$content = get_string_between($contents,'== How to ==','== EOF ==');
	$Parsedown = new Parsedown();
	return $Parsedown->text($content);
}

function plugin_get_faq() {	
	require_once( ABSPATH . 'wp-content/plugins/clean-slider/inc/Parsedown.php' );
	$contents = file_get_contents(ABSPATH . 'wp-content/plugins/clean-slider/readme.txt', FILE_USE_INCLUDE_PATH);	
	$content = get_string_between($contents,'== Frequently Asked Questions ==','= Do you offer support =');
	$content = str_replace('= ','<h4>',$content);
	$content = str_replace(' =','</h4>',$content);
	$Parsedown = new Parsedown();
	return $Parsedown->text($content);
}

function plugin_changelog() {	
	require_once( ABSPATH . 'wp-content/plugins/clean-slider/inc/Parsedown.php' );
	$contents = file_get_contents(ABSPATH . 'wp-content/plugins/clean-slider/readme.txt', FILE_USE_INCLUDE_PATH);	
	$content = get_string_between($contents,'== Changelog ==','== How to ==');
	$content = str_replace('== ','<h5>',$content);
	$content = str_replace(' ==','</h5>',$content);
	$content = str_replace('= ','<h4>',$content);
	$content = str_replace(' =','</h4>',$content);
	$Parsedown = new Parsedown();
	return $Parsedown->text($content);
}
global $wpdb;
$table_name_slides = $wpdb->prefix . "clean_slides_vava";
$sliderListing = $wpdb->get_results(  " SELECT id, name, selector, status FROM  $table_name_slides ORDER BY id ASC ");
?>
<style> .accordion-section-title span.dashicons { margin-right: 10px; } .wp-list-table thead th span.dashicons { font-size: 16px; padding-top: 3px; } .subsubsub { margin-bottom: 15px; } .accordion-section-content h2 { font-size: 1.2em; } .accordion-section-content h4 { margin-bottom: 0px; } button.confirm { margin-bottom: 30px !important; } </style>
<h2>Slides <a href="admin.php?page=clean_slider&action=add_slide" class="add-new-h2">Add New Slide</a></h2>
<div class="accordion-container" style="border-top: 4px solid #DFDFDF; margin-top: 15px;">
	<div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-format-gallery"></span> View Sliders</h3>
	</div>
	<div class="accordion-section-content">
        <ul class="subsubsub">
            <li class="all" style="cursor: pointer;">All |</li>
            <li class="moderated"><a href="#" rel="active">Active (<span id="count-active"><?php echo countStatus(2); ?></span>)</a> |</li>
            <li class="approved"><a href="#" rel="unapproved">Disabled (<span id="count-disabled"><?php echo countStatus(1); ?></span>)</a></li>
        </ul>        
        <p class="search-box"><span id="show-help" style="cursor: pointer; font-size: 26px;" class="dashicons dashicons-universal-access"></span></p>
        <table class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th width="25" style="text-align:center;"><span class="dashicons dashicons-list-view"></span></th>
                <th class="manage-column"> Title</th>
                <th class="manage-column" width="90" style="text-align:center;"><span class="dashicons dashicons-images-alt2"></span> Images</th>
                <th class="manage-column" style="text-align:right;">Shortcode <span class="dashicons dashicons-admin-plugins"></span></th>                
            </tr>
            </thead>
            <tbody id="the-comment-list">
                <?php
				if ( empty($sliderListing) ){
					echo '<tr class="alternate"><td colspan="4" style="padding-bottom: 20px; padding-top: 20px;"><em>There is no defined sliders yet. Do you wish to <a href="admin.php?page=clean_slider&amp;action=add_slide">add new</a> slider now?</em></td></tr>';
				} else {
					$i = 1;
					foreach ( $sliderListing as $slider ) {
						$slider_id = $slider->id;
						$slider_name = $slider->name;
						$slider_status = $slider->status;
						$slider_selector = $slider->selector;						
						$slider_class = ' class="active" id="sliders'.$slider_id.'"';
						if($slider_status == '1') { $slider_class = ' class="unapproved" id="sliders'.$slider_id.'"'; }
						echo '<tr'.$slider_class.'>';
						echo '<td align="center">'.$i.'</td>';
						echo '<td>';
						echo '#'.$slider_selector.'&nbsp;&nbsp;&nbsp;';
						echo '<strong>'.$slider_name.'</strong>';
						echo '<div class="row-actions">';
						echo '<span class="approve"><a href="#" rel="'.$slider_id.'" class="activateSlideLink">Activate</a></span>';
						echo '<span class="unapprove"><a href="#" rel="'.$slider_id.'" class="disableSlideLink">Disable</a></span>';
						echo '<span class="edit"> | <a href="admin.php?page=clean_slider&vava='.$slider_id.'&active_acc=settings">Edit</a> |</span>';
						echo '<span class="trash"> <a href="#" rel="'.$slider_id.'" data-status="'.$slider_status.'" class="deleteSlider">Delete</a></span>';
						echo '</div>';
						echo '</td>';
						echo '<td align="center" style="font-size: 15px;">';
						echo '<span style="display: block; float: left; margin-top: 8px; position: relative;">'.countImages($slider_id).'</span>';
						if(countImages($slider_id) == '') { echo '<a class="button" href="admin.php?page=clean_slider&vava='.$slider_id.'&active_acc=add" style="background: #E0E0E0; font-size: 12px; font-weight: bold; position: relative; text-transform: uppercase; top: 6px;">add images</a>'; } 
						else { echo '<a class="button" href="admin.php?page=clean_slider&vava='.$slider_id.'" style="background: #E0E0E0; font-size: 12px; font-weight: bold; position: relative; text-transform: uppercase; top: 6px;">view</a>'; }
						echo '</td>';
						echo '<td align="right"><input type="text" value="[vava slider=&quot;'.$slider_id.'&quot;]" onClick="this.setSelectionRange(0, this.value.length)" style="margin-top: 6px; position: relative; text-align:center;" /></td>';
						echo '</tr>';
						$i++;
					}
				} ?>        
            </tbody>
        </table>
        <br />
	</div>	
    <div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-universal-access-alt"></span> Help &amp; Support</h3>
	</div>
    <div class="accordion-section-content">
    	<p class="search-box"><span id="show-docs" style="cursor: pointer; font-size: 26px;" class="dashicons dashicons-editor-help"></span></p>        
        <p><?php echo plugin_get_help(); ?></p>
        <h2>FAQ</h2>
        <p><?php echo plugin_get_faq(); ?></p>
    </div>    
    <div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-book"></span> Documentation</h3>
	</div>
    <div class="accordion-section-content">
    	<p class="search-box"><span id="show-req" style="cursor: pointer; font-size: 26px;" class="dashicons dashicons-share-alt"></span></p>
        <p><?php echo plugin_get_description(); ?></p>
        <h2>Changelog / Upgrade</h2>
        <?php echo plugin_changelog(); ?>
    </div>    
    <div class="accordion-section">
		<h3 class="accordion-section-title"><span class="dashicons dashicons-smiley"></span> Request feature</h3>
	</div>
    <div class="accordion-section-content">
        <p>You can ask for additional help or change via Paypal donation to cover my time.</p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="MUX5R82VA27DE">
            <table cellpadding="2">
            <tr><td><input type="hidden" name="on0" value="Buy me a">Buy me a</td></tr><tr><td><select name="os0">
                <option value="Coffe">Coffe $2.00 USD</option>
                <option value="Juice">Juice $5.00 USD</option>
                <option value="Sandwich">Sandwich $10.00 USD</option>
                <option value="T-Shirt">T-Shirt $20.00 USD</option>
                <option value="Shoes">Shoes $50.00 USD</option>
                <option value="PC upgrade">PC upgrade $100.00 USD</option>
            </select> </td></tr>
            <tr><td><br /><input type="hidden" name="on1" value="Choose service">Choose service</td></tr><tr><td><select name="os1">
                <option value="One time">One time </option>
                <option value="Couple things">Couple things </option>
                <option value="Long time">Long time </option>
            </select> </td></tr>
            </table>
            <input type="hidden" name="currency_code" value="USD">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/logo/paypal_logo.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
</div>
<p style="color: #f0f0f0; font-size: 8px; letter-spacing: 0.5em; opacity: 1; text-align:center; text-transform: uppercase;">made in : bih &middot; by : staging</p>       