<?php
/**
 * Plugin Name: Wordpress Timeline
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: A wordpress plugin for creating timelines quickly and easily using wordpress functionality and TimelineJS
 * Version: 0.1A
 * Author: Will Kruse
 * Author URI: http://wskruse.github.io
 * License: MIT
 */

if(!defined('WPT_START_DATE_META')) define('WPT_START_DATE_META', 'wpt_start_date');
if(!defined('WPT_END_DATE_META')) define('WPT_END_DATE_META', 'wpt_end_date');
if(!defined('WPT_MEDIA_LINK_META')) define('WPT_MEDIA_LINK_META', 'wpt_media_link');
if(!defined('WPT_MEDIA_CAPTION_META')) define('WPT_MEDIA_CAPTION_META', 'wpt_media_caption');
if(!defined('WPT_MEDIA_CREDIT_META')) define('WPT_MEDIA_CREDIT_META', 'wpt_media_credit');
if(!defined('WPT_MEDIA_THUMB_META')) define('WPT_MEDIA_THUMB_META', 'wpt_media_thumb');

//add the custom post type wpt_slide, as well as perform any other init actions
function wpt_init() {
	register_post_type('wpt_slide',
		array(
			'labels' => array(
				'name' => __('Slides'),
				'singular_name' => __('Slide'),
				'add_new_item' => 'Add New Slide',
				'edit_item' => 'Edit Slide',
				'new_item' => 'New Slide',
				'view_item' => 'View Slide',
			),
			'rewrite' => array('slug' => 'slide'),
			'supports' => array(
				'title','editor','custom-fields'
			),
			'register_meta_box_cb' => 'wpt_add_slide_meta_boxes',
			'has_archive' => FALSE,
			'exclude_from_search' => TRUE,
			'publicly_queryable' => FALSE,
			'show_ui' => TRUE,
			'show_in_nav_menus' => TRUE
		)
	);
}
add_action('init', 'wpt_init');
//callback from register_post_type to add meta boxes to the slide post type
function wpt_add_slide_meta_boxes() {
	//start adding meta boxes
	//start date meta box
	add_meta_box(
		'wpt_start_date_section',//html id of section
		'Start Date',//label of section
		'wpt_meta_box_datepicker',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array('nonce_action'=>'wpt_start_date', 'nonce_name'=>'wpt_start_date_nonce', 'meta_key'=>WPT_START_DATE_META)//args to pass to the callback
	);
	//end date meta box
	add_meta_box(
		'wpt_end_date_section',//html id of section
		'End Date',//label of section
		'wpt_meta_box_datepicker',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array('nonce_action'=>'wpt_end_date', 'nonce_name'=>'wpt_end_date_nonce', 'meta_key'=>WPT_END_DATE_META)//args to pass to the callback
	);
	//media link meta box
	add_meta_box(
		'wpt_media_link_section',//html id of section
		'Media Link',//label of section
		'wpt_meta_box_text',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array('nonce_action'=>'wpt_media_link', 'nonce_name'=>'wpt_media_link_nonce', 'meta_key'=>WPT_MEDIA_LINK_META)//args to pass to the callback
	);
	//media caption meta box
	add_meta_box(
		'wpt_media_caption_section',//html id of section
		'Media Caption',//label of section
		'wpt_meta_box_text',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array('nonce_action'=>'wpt_media_caption', 'nonce_name'=>'wpt_media_caption_nonce', 'meta_key'=>WPT_MEDIA_CAPTION_META)//args to pass to the callback
	);
	//media credit meta box
	add_meta_box(
		'wpt_media_credit_section',//html id of section
		'Media Caption',//label of section
		'wpt_meta_box_text',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array('nonce_action'=>'wpt_media_credit', 'nonce_name'=>'wpt_media_credit_nonce', 'meta_key'=>WPT_MEDIA_CREDIT_META)//args to pass to the callback
	);
	//media caption meta box
	add_meta_box(
		'wpt_media_thumb_section',//html id of section
		'Media Caption',//label of section
		'wpt_meta_box_text',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array('nonce_action'=>'wpt_media_thumb', 'nonce_name'=>'wpt_media_thumb_nonce', 'meta_key'=>WPT_MEDIA_THUMB_META)//args to pass to the callback
	);
}

function wpt_meta_box_datepicker($post, $box) {
	wp_nonce_field($box['args']['nonce_action'], $box['args']['nonce_name']);
	$value = get_post_meta($post->ID, $box['args']['meta_key'], TRUE);
	?>
		<input type="text" class="wpt-datepicker" name="<?php print $box['args']['meta_key']; ?>" id="<?php print $box['args']['meta_key'].'_'.$post->ID; ?>" value="<?php print $value; ?>">
	<?php
}

function wpt_meta_box_text($post, $box) {
	wp_nonce_field($box['args']['nonce_action'], $box['args']['nonce_name']);
	$value = get_post_meta($post->ID, $box['args']['meta_key'], TRUE);
	?>
		<input type="text" class="wpt-text" name="<?php print $box['args']['meta_key']; ?>" id="<?php print $box['args']['meta_key'].'_'.$post->ID; ?>" value="<?php print $value; ?>">
	<?php
}

function wpt_rewrite_flush() {
	//ensure that the cpt is added before flush
	wpt_init();
	//flush rewrite rules to take into account the new cpt ruls
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wpt_rewrite_flush');
