<?php
/*
Plugin Name: Custom Post Type - Speakers
Plugin URI: http://9seeds.com/plugins/
Description: Custom Post Type Plugin to create event speakers
Version: 1.1
Author: 9seeds.com
Author URI: http://9seeds.com/
*/

/* create the speaker post type */
function post_type_speakers() {
     $labels = array(
    'name' => _x('Speakers', 'post type general name'),
    'singular_name' => _x('Speakers', 'post type singular name'),
    'add_new' => _x('Add New', 'speakers'),
    'add_new_item' => __('Add New Speaker'),
    'edit_item' => __('Edit Speaker'),
    'edit' => _x('Edit', 'speakers'),
    'new_item' => __('New Speaker'),
    'view_item' => __('View Speaker'),
    'search_items' => __('Search Speakers'),
    'not_found' =>  __('No speakers found'),
    'not_found_in_trash' => __('No speakers found in Trash'),
    'view' =>  __('View Speaker'),
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array("slug" => "speaker"),
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'thumbnail' )
  );

  register_post_type( 'speakers', $args);
  
	add_image_size( 'speaker_thumbnail', 35, 35, false );
}

add_action( 'init', 'post_type_speakers', 1 );

/* create custom meta boxes */

function custom_meta_boxes_speakers() {
    add_meta_box("speakers-details", "Speaker Details", "meta_cpt_speakers", "speakers", "normal", "low");
}

add_action('admin_menu', 'custom_meta_boxes_speakers');

function meta_cpt_speakers() {
    global $post;

	echo '<input type="hidden" name="speakers_noncename" id="speakers_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

	echo '<label for="speaker_bio">Speaker Bio</label><br />';
	echo '<textarea style="width: 55%;" rows="10" cols="50" name="speaker_bio" />'.get_post_meta($post->ID, 'speaker_bio', true).'</textarea><br /><br />';

	echo '<label for="speaker_pres_title">Presentation Title</label><br />';
	echo '<input style="width: 55%;" type="text" name="speaker_pres_title" value="'.get_post_meta($post->ID, 'speaker_pres_title', true).'" /><br /><br />';

	echo '<label for="speaker_pres_description">Presentation Description</label><br />';
	echo '<textarea style="width: 55%;" rows="10" cols="50" name="speaker_pres_description" />'.get_post_meta($post->ID, 'speaker_pres_description', true).'</textarea><br /><br />';

	echo '<label for="speaker_url">Website (<strong>Use http://</strong>)</label><br />';
	echo '<input style="width: 55%;" type="text" name="speaker_url" value="'.get_post_meta($post->ID, 'speaker_url', true).'" /><br /><br />';

	echo '<label for="speaker_twitter_url">Twitter ID (<strong>No @ symbol, just the handle</strong>)</label><br />';
	echo '<input style="width: 55%;" type="text" name="speaker_twitter_url" value="'.get_post_meta($post->ID, 'speaker_twitter_url', true).'" /><br /><br />';

	echo '<label for="speaker_pres_url">Presentation URL (<strong>Use http://</strong>)</label><br />';
	echo '<input style="width: 55%;" type="text" name="speaker_pres_url" value="'.get_post_meta($post->ID, 'speaker_pres_url', true).'" /><br /><br />';
}

/* When the post is saved, saves our speaker data */
function save_speaker_postdata($post_id, $post) {
   	if ( !wp_verify_nonce( $_POST['speakers_noncename'], plugin_basename(__FILE__) )) {
	return $post->ID;
	}

	/* confirm user is allowed to save page/post */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post->ID ))
		return $post->ID;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	}

	/* ready our data for storage */
	foreach ($_POST as $key => $value) {
        $mydata[$key] = $value;
    }

	/* Add values of $mydata as custom fields */
	foreach ($mydata as $key => $value) {
		if( $post->post_type == 'revision' ) return;
		$value = implode(',', (array)$value);
		if(get_post_meta($post->ID, $key, FALSE)) {
			update_post_meta($post->ID, $key, $value);
		} else {
			add_post_meta($post->ID, $key, $value);
		}
		if(!$value) delete_post_meta($post->ID, $key);
	}
}

add_action('save_post', 'save_speaker_postdata', 1, 2); // save the custom fields

/* move template files on activation */
register_activation_hook(__FILE__, "speaker_activation");

function speaker_activation()
{
	//move files around
	$pluginpath = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/';
	$themepath =  get_template_directory()."/";

	$templates = glob($pluginpath."template*");
	foreach($templates as $t)
	{
		copy($t,$themepath.basename($t));
	}

	$singles = glob($pluginpath."single-speakers.php");
	foreach($singles as $s)
	{
		copy($s,$themepath.basename($s));
	}
}

/*
 * Shortcode for adding speaker info. For example,
 * could be used on a schedule page.
 * 
 * Format: [speaker_snippet id="3"]
 * 
 * id = the post id for the speaker
 * 
 * will return thumbnail, speaker name, session title
 * 
 */

function speaker_snippet($atts, $content = null) {
	extract( shortcode_atts( array(
      'id' => 0,
      ), $atts ) );
      
	if ( !$id ) {
		return;
	}
	$id = absint( $id );
	
	$output = '<div class="speaker_snippet">';
	$output .= '<div class="thumbnail"><a href="' . get_permalink( $id ) . '">' . get_the_post_thumbnail( $id, 'speaker_thumbnail' ) . '</a></div>';
	$output .= '<div class="name"><a href="' . get_permalink( $id ) . '">' . get_the_title( $id ) . '</a></div>';
	$output .= '<div class="title">' . get_post_meta( $id, 'speaker_pres_title', true ) . '</div>';
	$output .= '</div>';
	
	return $output;
}
add_shortcode( 'speaker_snippet', 'speaker_snippet' );

/* template tags */
function speaker_twitter_link() {
	global $post;
	
	$twitter = get_post_meta( $post->ID, 'speaker_twitter_url', true );
	$twitter = apply_filters( 'speaker_twitter', $twitter );
	
	$twitter_tag = '<a href="http://twitter.com/' . $twitter . '" target="_blank">Twitter</a>';
	$twitter_tag = apply_filters( 'speaker_twitter_tag', $twitter_tag );
	
	if ( $twitter ) {
		echo $twitter_tag;
	}
}

function speaker_url_link() {
	global $post;

	$url = get_post_meta( $post->ID, 'speaker_url', true );
	$url = apply_filters( 'speaker_url', $url );
	
	$url_tag = '<a href="' . $url . '" target="_blank">Website</a>';
	$url_tag = apply_filters( 'speaker_url_tag', $url_tag );
	
	if ( $url ) {
		echo $url_tag;
	}
}
function speaker_presentation_link() {
	global $post;
	
	$pres = get_post_meta( $post->ID, 'speaker_pres_url', true );
	$pres = apply_filters( 'speaker_presentation', $pres );
	
	$pres_tag = '<a href="' . $pres . '" target="_blank">Presentation Slides</a>';
	$pres_tag = apply_filters( 'speaker_presentation_tag', $pres_tag );
	
	if ( $pres ) {
		echo $pres_tag;
	}
}

function speaker_bio() {
	global $post;
	
	$output = wpautop( get_post_meta( $post->ID, 'speaker_bio', true ) );
	
	$output = apply_filters( 'speaker_bio', $output );
	
	echo $output;
}

function speaker_presentation_title() {
	global $post;
	
	$label = '<strong>Session Title:</strong>';
	$label = apply_filters( 'speaker_pres_title_label', $label );
	
	$output = get_post_meta( $post->ID, 'speaker_pres_title', true );
	
	$output = apply_filters( 'speaker_presentation_title', $output );
	
	echo $label . ' ' . $output;
}

function speaker_presentation_description() {
	global $post;
	
	$output = wpautop( get_post_meta( $post->ID, 'speaker_pres_description', true ) );
	
	$output = apply_filters( 'speaker_presentation_description', $output );
	
	echo $output;
}

?>