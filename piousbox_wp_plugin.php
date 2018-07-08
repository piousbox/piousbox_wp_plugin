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
    'slug'    => 'tools',
    'n_posts' => 1
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  # var_dump( $cat );
  $args = array(
    'numberposts'      => $attrs['n_posts'],
    'offset'           => $attrs['idx'],
    'category'         => $cat->term_id,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );

  $postsRendered = '';
  foreach ($recent_posts as &$post) {
    $subtitle = new WP_Subtitle( $post['ID'] );
    $s = $subtitle->get_subtitle();
    $tmp = <<<EOT
    <div>
      <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
      <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
    </div>
EOT;
    $postsRendered = "$postsRendered$tmp<br /><br />";
  }

  $out = <<<EOT
    <div class="CategoryWidget"><h1 class="header">{$cat->name}</h1>{$postsRendered}</div>
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
    'numberposts'      => 1,
    'offset'           => $attrs['idx'],
    'tag'              => $features_tag,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
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

/**
 * Recent Posts
 */
function recent_posts_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'n_posts' => 5
  ), $raw_attrs );

  $args = array(
    'numberposts' => $attrs['n_posts'],
    'orderby'     => 'post_date',
    'order'       => 'DESC',
    'post_type'   => 'post',
    'post_status'      => 'publish',
  );
  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
  $postsRendered = '';
  foreach ($recent_posts as &$post) {

    // var_dump($post);

    $subtitle = new WP_Subtitle( $post['ID'] );
    $author = get_the_author_meta('display_name', $post->author);
    $s = $subtitle->get_subtitle();
    $date = substr($post['post_date'], 0, 10);
    $tmp = <<<EOT
    <div class="item" >
      <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
      <div class="meta" >By $author on {$date}</div>
      <div class="description"><a href="/index.php?p={$post['ID']}">$s</a></div>
      <div class="divider"></div>
    </div>
EOT;
    $postsRendered = "$postsRendered$tmp";
  }

  $out = <<<EOT
    <div class="RecentPosts">
      <div class="header" >Recent Posts</div>
      {$postsRendered}
    </div>
EOT;

  return $out;
}
add_shortcode( 'recent_posts', 'recent_posts_shortcode' );

/**
 * CategoryVideo Widget
 * 20180707 _vp_
 */
function category_video_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'    => 'scrum-diary',
    'n_posts' => 1
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $args = array(
    'numberposts'      => $attrs['n_posts'],
    'offset'           => $attrs['idx'],
    'category'         => $cat->term_id,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
      
  $postsRendered = '';
  foreach ($recent_posts as &$post) {
    $author   = get_the_author_meta('display_name', $post->author);
    $date     = substr($post['post_date'], 0, 10);
    $subtitle = new WP_Subtitle( $post['ID'] );
    $s        = $subtitle->get_subtitle();
    $content  = $post['post_content'];
    $video    = trim( strtok($content, "\n") );

    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
    preg_match( $pattern, $video, $matches);
    
    $content = "<div class='thumb'><img src='https://img.youtube.com/vi/{$matches[1]}/0.jpg' alt='' /></div>";

    $tmp = <<<EOT
    <div>
      <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
      <div class="meta" >By $author on {$date}</div>
      <div class="description">
        <a href="/index.php?p={$post['ID']}">$content</a><br />

        <ul class="actions">
          <li>
            <a href="/index.php?p={$post['ID']}">Play Video</a>
          </li>
        </ul>

      </div>
    </div>
EOT;
    $postsRendered = "$postsRendered$tmp<br /><br />";
  }

  $out = <<<EOT
    <div class="CategoryVideoWidget">
      <h1 class="header">{$cat->name}</h1>
      {$postsRendered}
    </div>
EOT;

  return $out;
}
add_shortcode( 'category_video_widget', 'category_video_widget_shortcode' );


/**
 * CategoryFull Widget
 * 20180707 _vp_
 */
function category_full_widget_shortcode( $raw_attrs ) {
  $attrs = shortcode_atts( array(
    'slug'    => 'diary',
    'n_posts' => 1
  ), $raw_attrs );
  $cat = get_category_by_slug( $attrs['slug'] );
  $args = array(
    'numberposts'      => $attrs['n_posts'],
    'offset'           => $attrs['idx'],
    'category'         => $cat->term_id,
    'orderby'          => 'post_date',
    'order'            => 'DESC',
    'post_type'        => 'post',
    'post_status'      => 'publish',
    'suppress_filters' => true
  );

  $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
      
  $postsRendered = '';
  foreach ($recent_posts as &$post) {
    $author   = get_the_author_meta('display_name', $post->author);
    $date     = substr($post['post_date'], 0, 10);
    $subtitle = new WP_Subtitle( $post['ID'] );
    $s        = $subtitle->get_subtitle();
    $content  = $post['post_content'];
    
    $tmp = <<<EOT
    <div>
      <h2><a href="/index.php?p={$post['ID']}">{$post['post_title']}</a></h2>
      <div class="meta" >By $author on {$date}</div>
      <div class="description">
        <a href="/index.php?p={$post['ID']}">$content</a><br />
      </div>
    </div>
EOT;
    $postsRendered = "$postsRendered$tmp<br /><br />";
  }

  $out = <<<EOT
    <div class="CategoryVideoWidget">
      <h1 class="header">{$cat->name}</h1>
      {$postsRendered}
    </div>
EOT;

  return $out;
}
add_shortcode( 'category_full_widget', 'category_full_widget_shortcode' );

