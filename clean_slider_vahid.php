<?php
/*
Plugin Name: Clean Slider for Wordpress
Plugin URI: http://staging.com.ba/
Description: Very clean responsive slideshow for designers. You will realise how simple is adding different layout on slides.
Version: 1.0
Author: STAGING
Author URI: http://staging.com.ba/
*/

if ( ! defined( 'ABSPATH' ) ) exit;

register_activation_hook(__FILE__,'clean_slider_vava_install');
register_deactivation_hook(__FILE__, 'clean_slider_vava_uninstall');

function clean_slider_vava_install(){	 
	 global $wpdb;
	 $table_name_slides = $wpdb->prefix . "clean_slides_vava"; 
	 $charset_collate = $wpdb->get_charset_collate();
	 $sql_slides = "CREATE TABLE IF NOT EXISTS $table_name_slides (
	  	id bigint(255) NOT NULL AUTO_INCREMENT,
	  	name tinytext NOT NULL,
		selector tinytext NOT NULL,
	  	duration mediumint(5) NOT NULL,
		fade mediumint(4) NOT NULL,
	  	centeredx varchar(1) DEFAULT '1' NOT NULL,
		centeredy varchar(1) DEFAULT '1' NOT NULL,
		status varchar(1) DEFAULT '1' NOT NULL,
	  	UNIQUE KEY id (id)
	) $charset_collate;";
	$wpdb->query($sql_slides);
	
	$table_name_images = $wpdb->prefix . "clean_images_vava"; 
	$sql_images = "CREATE TABLE IF NOT EXISTS $table_name_images (
	  	id bigint(255) NOT NULL AUTO_INCREMENT,
		slider bigint(255) NOT NULL,
	  	description text NULL,
		image varchar(16) NOT NULL,
		pos_ord int(16) DEFAULT '0' NULL,
		use_css int(1) DEFAULT '0' NULL,
	  	UNIQUE KEY id (id)
	) $charset_collate;";
	$wpdb->query($sql_images);
}

function clean_slider_vava_uninstall(){
	global $wpdb;
	$table_name_slides = $wpdb->prefix . "clean_slides_vava";
	$sql = "DROP TABLE $table_name_slides";
	$wpdb->query($sql);
	$table_name_images = $wpdb->prefix . "clean_images_vava";
	$sql = "DROP TABLE $table_name_images";
	$wpdb->query($sql);
}

add_action('admin_menu', 'clean_slider_vava_menu');
function clean_slider_vava_menu() {
    $page_hook_suffix = add_menu_page('Clean Slider', 'Slides', 'manage_options', 'clean_slider', 'clean_slider_vava_index', 'dashicons-format-gallery', '16'); 
	if(is_admin()) {   
    	add_action('admin_print_scripts-' . $page_hook_suffix, 'clean_slider_vava_admin_scripts');   
	}
}

add_action( 'admin_init', 'slide_init' );
function slide_init() {
    if ( current_user_can( 'delete_posts' ) )
        add_action( 'delete_post', 'slide_sync', 10 );
}

function slide_sync( $pid ) {
    global $wpdb;
    if ($image_count =  $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM wp_clean_images_vava WHERE image = %d', $pid ) ) ) {		
        return $wpdb->query( $wpdb->prepare( 'DELETE FROM wp_clean_images_vava WHERE id = %d', $image_count ) );
    }
    return true;
}

function update_status() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		$( "body" ).on( "click", ".activateSlideLink", function(e) {
			var slider_id = $(this).attr("rel");
			var slider = $(this);						
			e.preventDefault();			
			swal({ title: "Are you sure?", text: "Please confirm activation of slider", type: "info", showCancelButton: true, confirmButtonColor: "#0073AA", confirmButtonText: "Yes, activate", cancelButtonText: "No, cancel", closeOnConfirm: false, closeOnCancel: false, animation: "slide-from-bottom"
			},
			function(isConfirm){
			  	if (isConfirm) {
					var data = { 'action': 'update_status', 'slider_id': slider_id, 'status': 2 };					
					jQuery.post(ajaxurl, data, function(response) { 
						swal({ title: "Clean Slider", text: "Successfully activated\n\nDon't forget to include inside content :)", type: "success", confirmButtonColor: "#0073AA", animation: "slide-from-top"});
						$('#slider'+slider_id).removeClass("unapproved").addClass("active");
						$("#the-comment-list tr.not_have").remove();
						$('#count-disabled').text(function(i,txt) { return parseInt(txt, 10) - 1; });
						$('#count-active').text(function(i,txt) { return parseInt(txt, 10) + 1; });
						slider.removeClass("activateSlideLink");
						slider.addClass("disableSlideLink");
						slider.css('color','#D98500');
						slider.html('Disable');
						slider.parent().closest('tr').find('td').css('background','#ffffff');
					});	
			  	} else { swal({ title: "Clean Slider", text: "Not activated yet :)", type: "error", confirmButtonColor: "#0073AA", animation: "fade"}); }
			});
		});
		$( "body" ).on( "click", ".disableSlideLink", function(e) {
			var slider_id = $(this).attr("rel");	
			var slider = $(this);					
			e.preventDefault();
			swal({ title: "Are you sure?", text: "Please confirm disabling slide", type: "warning", showCancelButton: true, confirmButtonColor: "#0073AA", confirmButtonText: "Yes, disable", cancelButtonText: "No, cancel", closeOnConfirm: false, closeOnCancel: false, animation: "slide-from-bottom"
			},
			function(isConfirm){
			  	if (isConfirm) {
				  	var data = { 'action': 'update_status', 'slider_id': slider_id, 'status': 1 };			
					jQuery.post(ajaxurl, data, function(response) { 
						swal({ title: "Clean Slider", text: "successfully disabled", type: "success", confirmButtonColor: "#0073AA", animation: "slide-from-top"});
						$('#slider'+slider_id).removeClass("active").addClass("unapproved");
						$("#the-comment-list tr.not_have").remove();
						$('#count-disabled').text(function(i,txt) { return parseInt(txt, 10) + 1; });
						$('#count-active').text(function(i,txt) { return parseInt(txt, 10) - 1; });
						slider.removeClass("disableSlideLink");
						slider.addClass("activateSlideLink");
						slider.html('Activate');
						slider.css('color','#006505');
						slider.parent().closest('tr').find('td').css('background','#FEF7F1');
					});				
			  	} else { swal({ title: "Clean Slider", text: "Not disabled yet :)", type: "error", confirmButtonColor: "#0073AA", animation: "fade"}); }
			});
		});	
		$(".subsubsub a").click(function(e){
			e.preventDefault();
			var $active = $(this).attr("rel");
			var $count = $("#the-comment-list tr."+$active+"").fadeIn().length;
			if($count == 0) {
				$("#the-comment-list tr:not(."+$active+")").hide();
				$("#the-comment-list tr.not_have").remove();
				if($active == 'unapproved') { var $active_fix = 'DISABLED';	} 
				else { var $active_fix = 'ACTIVE'; }
				var row = '<tr class="not_have"><td colspan="4" class="alternate" style="padding-bottom: 20px; padding-top: 20px;"><em>There is no <strong>'+$active_fix+'</strong> slides...</em></td></tr>';
				$('#the-comment-list tr:first').after(row);				
			} else {			
				$("#the-comment-list tr.not_have").remove();
				$("#the-comment-list tr:not(."+$active+")").hide();
				$("#the-comment-list tr."+$active+"").fadeIn();
			}
		});		
		$(".subsubsub li.all").click(function(){
			$("#the-comment-list tr.not_have").remove();
			$("#the-comment-list tr").fadeIn();
		});		
		$(".accordion-container").accordion({ heightStyle: "content" });		
		$("#show-help").click(function(e){ e.preventDefault(); $( ".accordion-container" ).accordion( "option", "active", 1 ); });		
		$("#show-docs").click(function(e){ e.preventDefault(); $( ".accordion-container" ).accordion( "option", "active", 2 ); });		
		$("#show-req").click(function(e){ e.preventDefault(); $( ".accordion-container" ).accordion( "option", "active", 3 ); });	
	});
	</script> <?php
}

function delete_slider() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		$(".deleteSlider").click(function(e){
			var slider_id = $(this).attr("rel");						
			e.preventDefault();
			swal({ title: "Are you sure?", text: "Please confirm to delete this slider and all images inside.\n\nNote: this action will not delete images permanently.", type: "warning", showCancelButton: true, confirmButtonColor: "#0073AA", confirmButtonText: "Yes, delete", cancelButtonText: "No, cancel", closeOnConfirm: false, closeOnCancel: false, animation: "slide-from-bottom"
			},
			function(isConfirm){
			  	if (isConfirm) {
				  	var data = { 'action': 'delete_slider', 'slider_id': slider_id, 'status': 1 };			
					$('#sliders'+slider_id).remove();
					$("#the-comment-list tr.not_have").remove();
					jQuery.post(ajaxurl, data, function(response) { 
						swal({ title: "Clean Slider", text: "Successfully deleted", type: "success", confirmButtonColor: "#0073AA", animation: "slide-from-top"});
						var $count = $("#the-comment-list tr").length;						
						if($count == 0) {
							var row = '<tr class="not_have"><td colspan="4" class="alternate" style="padding-bottom: 20px; padding-top: 20px;"><em>There is no slides... Do you wish to <a href="admin.php?page=clean_slider&amp;action=add_slide">add new</a> slider now?</em></td></tr>';
							$('#the-comment-list').html(row);	
							var $count_active = $("#the-comment-list tr.active").length;
							var $count_inactive = $("#the-comment-list tr.unapproved").length;
							$('#count-disabled').text($count_inactive);
							$('#count-active').text($count_active);
						} else {							
							var $count_active = $("#the-comment-list tr.active").length;
							var $count_inactive = $("#the-comment-list tr.unapproved").length;
							$('#count-disabled').text($count_inactive);
							$('#count-active').text($count_active);
						}
					});
			  	} else { swal({ title: "Clean Slider", text: "Not deleted yet :)", type: "error", confirmButtonColor: "#0073AA", animation: "fade"}); }
			});
		});	
	});
	</script> <?php
}

function update_position() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		$('#set').sortable({
			opacity: 0.7,
			tolerance: "pointer",
			cursor: "move",
			forcePlaceholderSize: true,
			placeholder: "sortable-placeholder",
			update: function() {
				var stringDiv = "";
				$("#set").children().each(function(i) {
					var li = $(this);					
					stringDiv += " "+li.attr("rel") + '=' + i + '&';
				});				
				var data = stringDiv+'action=update_position';		
				jQuery.post(ajaxurl, data, function(response) { 
					swal({ title: "Clean Slider", text: "Images successfully sorted", type: "success", confirmButtonColor: "#0073AA", animation: "slide-from-top"});	
				});
			}
		}); 
		$( "#set" ).disableSelection();
	});
	</script> <?php
}

function delete_image() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {
		$(".sliderImagesDelete").click(function(e){
			var slider_image_id = $(this).attr("rel");						
			e.preventDefault();
			swal({ title: "Are you sure?", text: "Please confirm to delete this slider image.\n\nNote: this action will not delete image permanently.", type: "warning", showCancelButton: true, confirmButtonColor: "#0073AA", confirmButtonText: "Yes, delete", cancelButtonText: "No, cancel", closeOnConfirm: false, closeOnCancel: false, animation: "slide-from-bottom"
			},
			function(isConfirm){
			  	if (isConfirm) {
				  	var data = { 'action': 'delete_image', 'slider_image': slider_image_id };
				  	jQuery.post(ajaxurl, data, function(response) { 
				  		swal({ title: "Clean Slider", text: "Image successfully deleted", type: "success", confirmButtonColor: "#0073AA", animation: "slide-from-top"});
						$('#sliderImage'+slider_image_id).fadeOut();		
				  	});	
			  	} else { swal({ title: "Clean Slider", text: "Not deleted yet :)", type: "error", confirmButtonColor: "#0073AA", animation: "fade"}); }
			});
		});
	});
	</script> <?php
}

function update_status_callback() {
	global $wpdb; 
	$slider_id = intval( $_POST['slider_id'] );
	$slider_status = intval( $_POST['status'] );		
	$table_name_slides = $wpdb->prefix . "clean_slides_vava";
	$wpdb->update( $table_name_slides, array( 'status' => $slider_status ), array( 'id' => $slider_id ), array( '%d' ), array( '%d' ) );	
	$response = array( 'what'=>'status', 'action'=>'update_status', 'id'=>$slider_id, 'data'=>'Status changed' );
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();	
	wp_die();
}
add_action( 'wp_ajax_update_status', 'update_status_callback' );

function delete_slider_callback() {
	global $wpdb; 
	$slider_id = intval( $_POST['slider_id'] );
	$table_name_images = $wpdb->prefix . "clean_images_vava";
	$table_name_slides = $wpdb->prefix . "clean_slides_vava";
	$wpdb->delete( $table_name_slides, array( 'id' => $slider_id ) );
	$wpdb->delete( $table_name_images, array( 'slider' => $slider_id ) );
	$response = array( 'what'=>'removed', 'action'=>'delete_slider', 'id'=>$slider_id, 'data'=>'Slider deleted' );
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();	
	wp_die();	
}
add_action( 'wp_ajax_delete_slider', 'delete_slider_callback' );

function update_position_callback() {
	global $wpdb; 	
	$slider_image = $_POST;	
	foreach ( $slider_image as $key => $value ) {
		$image_order = intval($value);
		$image_id = intval($key);			
		$table_name_images = $wpdb->prefix . "clean_images_vava";		
		$wpdb->update( $table_name_images, array( 'pos_ord' => $image_order ), array( 'id' => $image_id ), array( '%d' ), array( '%d' ) );		
    }	
	$response = array( 'what'=>'position', 'action'=>'update_position', 'data'=>'Position saved' );
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();		
	wp_die();
}
add_action( 'wp_ajax_update_position', 'update_position_callback' );

function delete_image_callback() {
	global $wpdb; 
	$slider_image = intval( $_POST['slider_image'] );
	$table_name_images = $wpdb->prefix . "clean_images_vava";
	$wpdb->delete( $table_name_images, array( 'id' => $slider_image ) );
	$response = array( 'what'=>'removed', 'action'=>'delete_image', 'id'=>$slider_image, 'data'=>'Slider image removed' );
	$xmlResponse = new WP_Ajax_Response($response);
	$xmlResponse->send();	
	wp_die();	
}
add_action( 'wp_ajax_delete_image', 'delete_image_callback' );


function clean_slider_vava_admin_scripts() {
    wp_register_script( 'vava-alert', plugins_url( '/clean-slider/js/sweetalert.min.js') );
	wp_register_style( 'vava-alert-style', plugins_url('/clean-slider/css/sweetalert.css') );
	wp_enqueue_script( array( 'jquery', 'vava-alert' ) );
	wp_enqueue_style( 'vava-alert-style' );
	add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}

function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) { return array_diff( $plugins, array( 'wpemoji' ) ); } 
  else { return array(); }
}

function get_slide_name($slide_id){
    global $wpdb;
	$table_name_slides = $wpdb->prefix . "clean_slides_vava";
	$slider_name = $wpdb->get_var( $wpdb->prepare(" SELECT name FROM $table_name_slides WHERE id = %d", $slide_id ));
	return $slider_name;
}

function enqueue_slider(){
	wp_register_script( 'vava-slider', plugins_url( '/clean-slider/js/backstretch.js') );
	wp_enqueue_script( array( 'jquery', 'vava-slider' ) );
}
add_action( 'wp_enqueue_scripts', 'enqueue_slider' );

function vava_shortcode( $atts ) {    
	if(!is_numeric($atts["slider"])) { return '<div class="error"><p>Slides error: Please check your shortcode.</p></div>'; }
	else { 
		global $wpdb;
		$slider_id = intval($atts["slider"]);
		$table_name_slides = $wpdb->prefix . "clean_slides_vava";
		$slider = $wpdb->get_row( $wpdb->prepare( "SELECT selector, duration, fade, centeredx, centeredy, status FROM $table_name_slides WHERE id = %d", $slider_id));
		if(!$slider) { return false; }
		elseif($slider->status == '1') { return false; }
		else {
			$selector = $slider->selector;
			$duration = $slider->duration;
			$fade = $slider->fade;
			$cenx = $slider->centeredx;
			$ceny = $slider->centeredy;					
			if(!is_numeric($duration) || !$duration) { $duration = '5000'; }
			if(!is_numeric($fade) || !$fade) { $fade = '650'; }			
			if($cenx == '2') { $cenx = 'true'; }
			else { $cenx = 'false'; }
			if($ceny == '2') { $ceny = 'true'; }
			else { $ceny = 'false'; }			
			$table_name_images = $wpdb->prefix . "clean_images_vava";
			$slider_images = $wpdb->get_results( $wpdb->prepare( "SELECT description, image, use_css FROM $table_name_images WHERE slider = %d ORDER BY pos_ord ASC", $slider_id ) );
			
			ob_start();
			if($slider_images){ 
				$i = 0;
				$slide_img = '';
				$slide_desc = '';
				foreach ( $slider_images as $slide ) {
					$slide_url = wp_get_attachment_url( $slide->image );
					$slide_con = htmlspecialchars_decode($slide->description, ENT_QUOTES);
					$slide_content = trim(preg_replace('/([\r\n\t])/', '', $slide_con));
					$slide_img .= '"'.$slide_url.'",';					
					if($slide->use_css == 1) { 
						$slide_desc .= "'<div class=styled-slide".$i.">".str_replace("'", '"', $slide_content)."</div>',"; ?>
						<style type="text/css">
							#<?php echo $selector; ?> { height: 450px; }
							.styled-slide<?php echo $i; ?> { position: relative; text-align: center; top: 120px; width: 100%; }
                            .styled-slide<?php echo $i; ?> h1 { color: #fff; font-family: Tahoma; font-size: 32px; margin: 0px; padding: 0px; text-align: center; text-transform: uppercase; }
							.styled-slide<?php echo $i; ?> h2 { color: #fff; font-family: Tahoma; font-size: 28px; margin: 0px; padding: 0px; text-align: center; text-transform: uppercase; }
							.styled-slide<?php echo $i; ?> h3 { color: #fff; font-family: Tahoma; font-size: 23px; margin: 0px; padding: 0px; text-align: center; text-transform: uppercase; }
							.styled-slide<?php echo $i; ?> p { color: #fff; font-family: Tahoma; font-size: 14px; margin: 0px; padding: 0px; text-align: center; }
							.styled-slide<?php echo $i; ?> a { background: #fff; color: #000; font-family: Tahoma; font-size: 13px; margin: 0px; padding: 4px 15px ; text-align: center; }
                        </style>
                    	<?php 
					} else { $slide_desc .= "'".str_replace("'", '"', $slide_content)."',"; }					
					$i++;
				}
				$slide_description = rtrim($slide_desc, ","); ?>
				<div id="<?php echo $selector; ?>" style="min-height: 120px; overflow: hidden; position: relative;">
                	<?php if($i <= 1) { 
						echo '<div class="'.$selector.'-caption">';
						echo rtrim(trim($slide_desc, "'"), "',"); 
						echo '</div>';
					} else {
						echo '<div class="'.$selector.'-caption" style="display: none; position: absolute; width: 100%;">';
						echo '</div>';
					} ?>                    
                </div>
				<script>
					jQuery(document).ready(function($){
						$(window).load(function(){
							<?php if($i > 1) { ?>
							var images = [ <?php echo rtrim($slide_img, ","); ?> ];
							var texts = [ <?php echo $slide_description; ?> ];
							var slideshow = $('#<?php echo $selector; ?>').backstretch(images, {duration: <?php echo $duration; ?>, fade: <?php echo $fade; ?>, centeredX: <?php echo $cenx; ?>, centeredY: <?php echo $ceny; ?>});
							$(window).on("backstretch.before", function(e, instance) { $(".<?php echo $selector; ?>-caption").html('').fadeOut(); });
							$(window).on("backstretch.show", function (e, instance) { if(instance.index == 0) { $(".<?php echo $selector; ?>-caption").html( texts[instance.index] ).fadeIn(800); } else { $(".<?php echo $selector; ?>-caption").html('').html( texts[instance.index] ).fadeIn(800); } });	
							<?php } else { ?>
							$("#<?php echo $selector; ?>").backstretch(<?php echo rtrim($slide_img, ","); ?>, {centeredX: <?php echo $cenx; ?>, centeredY: <?php echo $ceny; ?>});
							<?php } ?>
						});
					});
				</script>
				<?php
			}
			return ob_get_clean();
		}
	}
}
add_shortcode( 'vava', 'vava_shortcode' );

function clean_slider_vava_index() {
	if (!current_user_can('manage_options') AND !is_admin()) { wp_die( __('You do not have sufficient permissions to access this page.') );	}
	$action = '';
	if(isset($_GET["action"])) { $action = sanitize_text_field($_GET["action"]); } 
	elseif(isset($_GET["vava"])) { $action = intval($_GET["vava"]); }
	echo '<div class="wrap">';    
	if(isset($action) && $action == 'add_slide') {
		echo '<h2>Slideshows &raquo; Add new slider</h2>';
		include('inc/_add_slider.php');
	} elseif(isset($action) && is_numeric($action)) {
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'editor' );
		add_action('admin_footer', 'update_position' );
		add_action('admin_footer', 'delete_image' );
		include('inc/_edit_slider.php');
	} elseif(isset($action) && $action == 'edit_slide') {
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'editor' );
		include('inc/_edit_slider_image.php');
	} else {
		wp_enqueue_script( 'jquery-ui-accordion' );
		add_action('admin_footer', 'update_status' );
		add_action('admin_footer', 'delete_slider' );
    	include('inc/_list_slider.php');
	}	
	echo '</div>';
}