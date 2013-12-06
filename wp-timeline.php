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
if(!defined('WPT_CPT_NAME')) define('WPT_CPT_NAME', 'wpt_slide');
if(!defined('WPT_DATE_NONCE_ACTION')) define('WPT_DATE_NONCE_ACTION', 'wpt_slide_dates');
if(!defined('WPT_DATE_NONCE_NAME')) define('WPT_DATE_NONCE_NAME', 'wpt_slide_dates_nonce');
if(!defined('WPT_MEDIA_NONCE_ACTION')) define('WPT_MEDIA_NONCE_ACTION', 'wpt_slide_media');
if(!defined('WPT_MEDIA_NONCE_NAME')) define('WPT_MEDIA_NONCE_NAME', 'wpt_slide_media_nonce');
if(!defined('WPT_START_DATE_META')) define('WPT_START_DATE_META', 'wpt_start_date');
if(!defined('WPT_END_DATE_META')) define('WPT_END_DATE_META', 'wpt_end_date');
if(!defined('WPT_MEDIA_LINK_META')) define('WPT_MEDIA_LINK_META', 'wpt_media_link');
if(!defined('WPT_MEDIA_CAPTION_META')) define('WPT_MEDIA_CAPTION_META', 'wpt_media_caption');
if(!defined('WPT_MEDIA_CREDIT_META')) define('WPT_MEDIA_CREDIT_META', 'wpt_media_credit');
if(!defined('WPT_MEDIA_THUMB_META')) define('WPT_MEDIA_THUMB_META', 'wpt_media_thumb');

//add the custom post type wpt_slide, as well as perform any other init actions
function wpt_init() {
	register_post_type(WPT_CPT_NAME,
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
				'title','editor'
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
		'wpt_date_section',//html id of section
		'Slide Dates',//label of section
		'wpt_meta_box_dates',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array()//args to pass to the callback
	);
	//end date meta box
	add_meta_box(
		'wpt_media_section',//html id of section
		'Media Information',//label of section
		'wpt_meta_box_media',//callback to generate html
		'wpt_slide',//post type to add meta box to
		'normal',//where to show the meta box (context)
		'default',//within context position of meta boxes
		array()//args to pass to the callback
	);
}

function wpt_save_slide_meta_fields($post_id) {
	//check if we are saving a slide
	if($_POST['post_type'] !== WPT_CPT_NAME) return;
	//check the two nonce fields
	if(!empty($_POST) && check_admin_referer(WPT_DATE_NONCE_ACTION,WPT_DATE_NONCE_NAME)) {
		//handle the date fields
		$start_date = sanitize_text_field($_POST[WPT_START_DATE_META]);
		$end_date = sanitize_text_field($_POST[WPT_END_DATE_META]);
		//convert the dates to timestamps for better portability
		$start_ts = strtotime($start_date);
		$start_ts = ($start_ts !== FALSE) ? $start_ts : '';
		$end_ts = strtotime($end_date);
		$end_ts = ($end_ts !== FALSE) ? $end_ts : '';
		update_post_meta($post_id, WPT_START_DATE_META, $start_ts);
		update_post_meta($post_id, WPT_END_DATE_META, $end_ts);
	}
	if(!empty($_POST) && check_admin_referer(WPT_MEDIA_NONCE_ACTION,WPT_MEDIA_NONCE_NAME)) {
		$media_link = sanitize_text_field($_POST[WPT_MEDIA_LINK_META]);
		$media_caption = sanitize_text_field($_POST[WPT_MEDIA_CAPTION_META]);
		$media_credit = sanitize_text_field($_POST[WPT_MEDIA_CREDIT_META]);
		$media_thumb = sanitize_text_field($_POST[WPT_MEDIA_THUMB_META]);
		update_post_meta($post_id, WPT_MEDIA_LINK_META, $media_link);
		update_post_meta($post_id, WPT_MEDIA_CAPTION_META, $media_caption);
		update_post_meta($post_id, WPT_MEDIA_CREDIT_META, $media_credit);
		update_post_meta($post_id, WPT_MEDIA_THUMB_META, $media_thumb);
	}

}
add_action('save_post', 'wpt_save_slide_meta_fields');

function wpt_meta_box_dates($post, $box) {
	wp_nonce_field(WPT_DATE_NONCE_ACTION, WPT_DATE_NONCE_NAME);
	$start_date = get_post_meta($post->ID, WPT_START_DATE_META, TRUE);
	$start_date = (!empty($start_date)) ? date('m/d/Y', $start_date) : '';
	$end_date = get_post_meta($post->ID, WPT_END_DATE_META, TRUE);
	$end_date = (!empty($end_date)) ? date('m/d/Y', $end_date) : '';
	?>
		<label class="wpt-datepicker-label"><span class="wpt-datepicker-label-text">Start</span> <input type="text" class="wpt-datepicker wpt-datepicker-start" name="<?php print WPT_START_DATE_META; ?>" id="<?php print WPT_START_DATE_META.'_'.$post->ID; ?>" value="<?php print $start_date; ?>"></label>
		<span class="wpt-datepicker-separator">-</span>
		<label class="wpt-datepicker-label"><input type="text" class="wpt-datepicker wpt-datepicker-end" name="<?php print WPT_END_DATE_META; ?>" id="<?php print WPT_END_DATE_META.'_'.$post->ID; ?>" value="<?php print $end_date; ?>"><span class="wpt-datepicker-label-text">End</span></label>
		<p class="wpt-admin-hint">Select the start and end dates for this slide</p>
	<?php
}

function wpt_meta_box_media($post, $box) {
	wp_nonce_field(WPT_MEDIA_NONCE_ACTION, WPT_MEDIA_NONCE_NAME);
	$media_link = get_post_meta($post->ID, WPT_MEDIA_LINK_META, TRUE);
	$media_caption = get_post_meta($post->ID, WPT_MEDIA_CAPTION_META, TRUE);
	$media_credit = get_post_meta($post->ID, WPT_MEDIA_CREDIT_META, TRUE);
	$media_thumb = get_post_meta($post->ID, WPT_MEDIA_THUMB_META, TRUE);
	?>
		<label><span class="wpt-label-text">Media Link</span> <input type="text" class="wpt-text-input" name="<?php print WPT_MEDIA_LINK_META; ?>" id="<?php print WPT_MEDIA_LINK_META.'_'.$post->ID; ?>" value="<?php print $media_link; ?>"></label>
		<p class="wpt-admin-hint">Enter a link to media you would like to display for this slide. This can be a link to a Flickr photo or set, a Vimeo, Dailymotion or Youtube video, a Soundcloud file, an Instagram photo, Twitter post or image from TwitPic or a Wikipedia article.</p>
		<label><span class="wpt-label-text">Media Caption</span> <input type="text" class="wpt-text-input" name="<?php print WPT_MEDIA_CAPTION_META; ?>" id="<?php print WPT_MEDIA_CAPTION_META.'_'.$post->ID; ?>" value="<?php print $media_caption; ?>"></label>
		<p class="wpt-admin-hint">Add a caption for this external media</p>
		<label><span class="wpt-label-text">Media Credit</span> <input type="text" class="wpt-text-input" name="<?php print WPT_MEDIA_CREDIT_META; ?>" id="<?php print WPT_MEDIA_CREDIT_META.'_'.$post->ID; ?>" value="<?php print $media_credit; ?>"></label>
		<p class="wpt-admin-hint">Please credit the original author/creator/owner of this content</p>
		<label><span class="wpt-label-text">Thumbnail Link</span> <input type="text" class="wpt-text-input" name="<?php print WPT_MEDIA_THUMB_META; ?>" id="<?php print WPT_MEDIA_THUMB_META.'_'.$post->ID; ?>" value="<?php print $media_thumb; ?>"></label>
		<p class="wpt-admin-hint">Add a link to a 32x32 pixel image to display on the timeline as a thumbnail</p>
	<?php
}

function wpt_activate_plugin() {
	//ensure that the cpt is added before flush
	wpt_init();
	//flush rewrite rules to take into account the new cpt ruls
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wpt_activate_plugin');

function wpt_deactivate_plugin() {

}
register_deactivation_hook(__FILE__, 'wpt_deactivate_plugin');
//Register my script for any js handling that needs to be done
// Register Script
function wpt_enqueue_admin_scripts() {
	$screen = get_current_screen();
	if($screen->id == 'wpt_slide') {
		wp_register_script( 'wpt-custom-js', plugins_url( 'admin.js', __FILE__), array( 'jquery-ui-datepicker' ), '0.1', false );
		wp_enqueue_script( 'wpt-custom-js' );
		wp_enqueue_style('wpt-jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css');
		wp_enqueue_style('wpt-custom-css', plugins_url('custom.css', __FILE__), array('wpt-jquery-ui-css'), '0.1');
	}

}
// Hook into the 'admin_enqueue_scripts' action
add_action( 'admin_enqueue_scripts', 'wpt_enqueue_admin_scripts' );

function get_slides_as_json() {
	$args = array(
		'post_type' => 'wpt_slide',
		'posts_per_page' => -1
	);
	$response = array(
		'timeline'=>array(
			'headline' => 'Timeline',
			'type' => 'default',
			'startDate' => '1967,1,1',
			'date' => array(),
			'era' => array()
		)
	);
	$query = new WP_Query($args);
	if($query->have_posts()) {
		while($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();
			$slide = array(
				'startDate' => date('Y,m,d', get_post_meta($post_id, WPT_START_DATE_META, TRUE)),
				'endDate' => date('Y,m,d', get_post_meta($post_id, WPT_END_DATE_META, TRUE)),
				'headline' => get_the_title(),
				'text' => get_the_excerpt(),
				'asset' => array(
					'thumbnail' => get_post_meta($post_id, WPT_MEDIA_THUMB_META, TRUE),
					'credit' => get_post_meta($post_id, WPT_MEDIA_CREDIT_META, TRUE),
					'caption' => get_post_meta($post_id, WPT_MEDIA_CAPTION_META, TRUE),
					'media' => get_post_meta($post_id, WPT_MEDIA_LINK_META, TRUE)
				)
			);
			$response['timeline']['date'][] = $slide;
		}
	}
	print json_encode($response);
	die();
}
add_action('wp_ajax_get_slides', 'get_slides_as_json');
add_action('wp_ajax_nopriv_get_slides', 'get_slides_as_json');

function wpt_timeline_shortcode() {
	return '<div id="wpt_timeline"></div>';
}
add_shortcode('timeline', 'wpt_timeline_shortcode');

function wpt_enqueue_front_scripts() {
	wp_enqueue_script('wpt-timelinejs', plugins_url('timelinejs/js/timeline.js', __FILE__), array('jquery'), '1.0', FALSE);
	wp_enqueue_script('wpt-storyjs', plugins_url('timelinejs/js/storyjs-embed.js', __FILE__), array('jquery', 'wpt-timelinejs'), '1.0', FALSE);
	wp_enqueue_script('wpt-inittjs', plugins_url('timeline.js', __FILE__), array('wpt-storyjs'), '1.0', FALSE);
	wp_enqueue_style('wpt-timeline-css', plugins_url('timelinejs/css/timeline.css', __FILE__), '1.0', FALSE);
	wp_localize_script('wpt-timelinejs', 'wpt_js', array('base_url'=>get_bloginfo('url')));
}
add_action( 'wp_enqueue_scripts', 'wpt_enqueue_front_scripts' );
