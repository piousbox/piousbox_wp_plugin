<?php
/**
 * Plugin Name: Piousbox Wordpress Plugin
 */
wp_register_style('myCSS', plugins_url('piousbox_wp_plugin/style.css'));
wp_enqueue_style( 'myCSS');

/*
 * [foobar]
 */
function foobar_func( $atts ){
  return "foo and bar";
}
add_shortcode( 'foobar', 'foobar_func' );


/*
 * [category_widget slug='interviewing']
 */
function category_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug' => 'tools'
  ), $raw_attrs );

  $args = array(
    'numberposts' => 1,
    'offset' => $attrs['idx'],
    'category' => $attrs['slug'],
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true
  );
  $cat = get_category_by_slug( $attrs['slug'] ); 

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $post = $recent_posts[0];
  $subtitle = new WP_Subtitle( $post['ID'] );
  $s = $subtitle->get_subtitle();

  $out = <<<EOT
<div class="CategoryWidget">
  <h1 class="header">{$cat->name}</h1>
  <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
  <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
</div>
EOT;
  return $out;
}
add_shortcode( 'category_widget', 'category_widget_shortcode' );

/*
 * [feature idx=0]
 */
function feature_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'idx' => 0
  ), $raw_attrs );

  $features_tag = 'Features';

  $args = array(
    'numberposts' => 1,
    'offset' => $attrs['idx'],
    'tag' => $features_tag,
    'orderby' => 'post_date',
    'order' => 'DESC',
    'post_type' => 'post',
    'post_status' => 'publish',
    'suppress_filters' => true
  );
  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $post = $recent_posts[0];
  $subtitle = new WP_Subtitle( $post['ID'] );
  $s = $subtitle->get_subtitle();
  $out = <<<EOT
<div>
  <h1><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h1>
  <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
</div>
EOT;
  return $out;
}
add_shortcode( 'feature', 'feature_shortcode' );


