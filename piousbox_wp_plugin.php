<?php
/**
 * Plugin Name: Piousbox Wordpress Plugin
 */
//[foobar]
function foobar_func( $atts ){
	return "foo and bar";
}
add_shortcode( 'foobar', 'foobar_func' );

// [feature idx=0]
function feature_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'idx' => 0
  ), $raw_attrs );

  $args = array(
	  'numberposts' => 1,
  	'offset' => $attrs['idx'],
	  'category' => 0,
	  'orderby' => 'post_date',
  	'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true
  );
  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $post = $recent_posts[0];
  
  return <<<EOT
<div>
  <h1>{$post['post_title']}</h1>
  <div class="description">{$post['post_content']}</div>
</div>
EOT;
}
add_shortcode( 'feature', 'feature_shortcode' );


